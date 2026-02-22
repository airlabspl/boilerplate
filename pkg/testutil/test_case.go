package testutil

import (
	"app/internal/router"
	"net/http/httptest"
	"testing"

	"github.com/playwright-community/playwright-go"
)

type TestCase struct {
	T          *testing.T
	Server     *httptest.Server
	Playwright *playwright.Playwright
	Browser    playwright.Browser
}

func NewTestCase(t *testing.T) *TestCase {
	err := playwright.Install()
	if err != nil {
		t.Fatalf("could not install playwright: %v", err)
	}

	pw, err := playwright.Run()
	if err != nil {
		t.Fatalf("could not start playwright: %v", err)
	}

	browser, err := pw.Chromium.Launch()
	if err != nil {
		t.Fatalf("could not launch browser: %v", err)
	}

	return &TestCase{
		T:          t,
		Server:     httptest.NewServer(router.New()),
		Playwright: pw,
		Browser:    browser,
	}
}

func (tc *TestCase) Close() {
	tc.Browser.Close()
	tc.Playwright.Stop()
	tc.Server.Close()
}

func (tc *TestCase) Visit(path string) BrowserPage {
	url := tc.Server.URL + path

	page, err := tc.Browser.NewPage()
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
