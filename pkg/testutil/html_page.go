package testutil

import (
	"net/http"
	"testing"
)

type HtmlPage struct {
	T        *testing.T
	Response *http.Response
}

func (hp HtmlPage) AssertHeaderValue(key, expected string) {
	value := hp.Response.Header.Get(key)
	if value == "" {
		hp.T.Fatalf("the header \"%v\" is not set", key)
	}

	if value != expected {
		hp.T.Fatalf("expected header \"%v\" to be: %v, got :%v instead", key, expected, value)
	}
}
