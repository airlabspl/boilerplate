package test

import (
	"app/pkg/testutil"
	"app/web"
	"io/fs"
	"net/http"
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

	t.Run("it caches assets", func(t *testing.T) {
		tc := testutil.NewTestCase(t)
		defer tc.Close()

		assetsFS, err := fs.Sub(web.FS, "dist/assets")
		files, err := fs.Glob(assetsFS, "*.js")
		if err != nil {
			t.Fatal(err)
		}
		if len(files) == 0 {
			t.Fatal("did not find any js files in dist/assets: build the web ui first")
		}

		jsFile := files[0]

		tc.Get("/assets/"+jsFile).
			AssertStatusCode(http.StatusOK).
			AssertHeaderValue("Cache-Control", "public, max-age=31536000, immutable")
	})
}
