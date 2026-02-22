package handler

import "net/http"

func New() http.Handler {
	h := http.NewServeMux()

	h.HandleFunc("GET /{$}", func(w http.ResponseWriter, r *http.Request) {
		w.Write([]byte("Hello, world"))
	})

	return h
}
