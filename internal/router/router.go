package router

import (
	"app/internal/handler"
	"net/http"
)

func New() *http.ServeMux {
	r := http.NewServeMux()

	r.HandleFunc("/", handler.SpaHandler())

	return r
}
