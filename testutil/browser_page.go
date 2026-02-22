package testutil

import (
	"testing"

	"github.com/playwright-community/playwright-go"
)

type BrowserPage struct {
	T    *testing.T
	Page playwright.Page
}

func (bp BrowserPage) AssertSee(expected string) {
	locator := bp.Page.GetByText(expected)
	if count, err := locator.Count(); err != nil || count == 0 {
		bp.T.Fatalf("could not find expected text: \"%v\"", expected)
	}
}
