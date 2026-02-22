package testutil

import (
	"app/handler"
	"net/http/httptest"
	"testing"

	"github.com/playwright-community/playwright-go"
)

type TestCase struct {
	T      *testing.T
	Server *httptest.Server
}

func NewTestCase(t *testing.T) *TestCase {
	return &TestCase{
		T:      t,
		Server: httptest.NewServer(handler.New()),
	}
}

func (tc *TestCase) Close() {

}

func (tc *TestCase) Visit(path string) BrowserPage {
	url := tc.Server.URL + path

	err := playwright.Install()
	if err != nil {
		tc.T.Fatalf("could not install playwright: %v", err)
	}

	pw, err := playwright.Run()
	if err != nil {
		tc.T.Fatalf("could not start playwright: %v", err)
	}
	browser, err := pw.Chromium.Launch()
	if err != nil {
		tc.T.Fatalf("could not launch browser: %v", err)
	}
	page, err := browser.NewPage()
	if err != nil {
		tc.T.Fatalf("could not create page: %v", err)
	}
	if _, err = page.Goto(url); err != nil {
		tc.T.Fatalf("could not goto: %v", err)
	}

	return BrowserPage{
		T:    tc.T,
		Page: page,
	}
}
