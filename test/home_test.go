package test

import (
	"app/testutil"
	"testing"
)

func TestHome(t *testing.T) {
	t.Run("it shows the landing page", func(t *testing.T) {
		tc := testutil.NewTestCase(t)
		defer tc.Close()

		tc.Visit("/").
			AssertSee("Hello, world")
	})
}
