package test

import (
	"app/pkg/testutil"
	"testing"
)

func TestSpa(t *testing.T) {
	t.Run("it does not cache index.html", func(t *testing.T) {
		tc := testutil.NewTestCase(t)
		defer tc.Close()

		tc.Get("/").
			AssertHeaderValue("Cache-Control", "no-cache, no-store, must-revalidate")

		tc.Get("/index.html").
			AssertHeaderValue("Cache-Control", "no-cache, no-store, must-revalidate")
	})
}
