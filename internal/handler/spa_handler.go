package handler

import (
	"app/web"
	"io/fs"
	"net/http"
)

func SpaHandler() http.HandlerFunc {
	sub, _ := fs.Sub(web.FS, "dist")
	root := http.FS(sub)
	fileServer := http.FileServer(root)

	return func(w http.ResponseWriter, r *http.Request) {
		path := r.URL.Path

		if path == "" || path == "/" {
			w.Header().Set("Cache-Control", "no-cache, no-store, must-revalidate")
			http.ServeFileFS(w, r, sub, "index.html")
			return
		}

		f, err := root.Open(path)
		if err != nil {
			w.Header().Set("Cache-Control", "no-cache, no-store, must-revalidate")
			http.ServeFileFS(w, r, sub, "index.html")
			return
		}
		defer f.Close()

		stat, err := f.Stat()
		if err == nil && stat.IsDir() {
			w.Header().Set("Cache-Control", "no-cache, no-store, must-revalidate")
			http.ServeFileFS(w, r, sub, "index.html")
			return
		}

		if path == "/index.html" {
			w.Header().Set("Cache-Control", "no-cache, no-store, must-revalidate")
		} else {
			w.Header().Set("Cache-Control", "public, max-age=31536000, immutable")
		}

		fileServer.ServeHTTP(w, r)
	}
}
