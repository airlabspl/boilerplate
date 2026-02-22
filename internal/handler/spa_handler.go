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
		}

		f, err := root.Open(path)
		if err != nil {
			r.URL.Path = "/"
			fileServer.ServeHTTP(w, r)
			return
		}

		w.Header().Set("Cache-Control", "no-cache, no-store, must-revalidate")

		if stat, err := f.Stat(); err != nil && stat.IsDir() {
			path = "/index.html"
		}

		fileServer.ServeHTTP(w, r)
	}
}
