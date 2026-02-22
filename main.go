package main

import (
	"app/handler"
	"log"
	"net/http"
)

func main() {
	h := handler.New()

	s := http.Server{
		Handler: h,
		Addr:    ":4000",
	}

	log.Fatal(s.ListenAndServe())
}
