package main

import (
	"app/internal/router"
	"context"
	"net/http"
	"os"
	"os/signal"
	"syscall"
)

func main() {
	s := http.Server{
		Handler: router.New(),
		Addr:    ":4000",
	}

	go s.ListenAndServe()

	notify := make(chan os.Signal, 1)
	signal.Notify(notify, os.Interrupt, syscall.SIGTERM)

	<-notify
	s.Shutdown(context.Background())
}
