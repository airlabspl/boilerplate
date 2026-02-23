package handler

import (
	"app/web"
	"io/fs"
	"net/http"
)

func SpaHandler() http.HandlerFunc {
	fs, _ := fs.Sub(web.FS, "dist")
	root := http.FS(fs)
	fileServer := http.FileServer(root)

	return func(w http.ResponseWriter, r *http.Request) {
		path := r.URL.Path

		if path == "" {
			path = "/index.html"
			w.Header().Set("Cache-Control", "no-cache, no-store, must-revalidate")
		}

		f, err := root.Open(path)
		if err != nil {
			r.URL.Path = "/"
			fileServer.ServeHTTP(w, r)
			w.Header().Set("Cache-Control", "no-cache, no-store, must-revalidate")
			return
		}

		if stat, err := f.Stat(); err != nil && stat.IsDir() {
			path = "/index.html"
			w.Header().Set("Cache-Control", "no-cache, no-store, must-revalidate")
		}

		if path == "/" || path == "/index.html" || path == "" {
			w.Header().Set("Cache-Control", "no-cache, no-store, must-revalidate")
		} else {
			w.Header().Set("Cache-Control", "max-age=360015768000, public")
		}

		fileServer.ServeHTTP(w, r)
	}
}
