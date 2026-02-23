package testutil

import (
	"net/http"
	"testing"
)

type HtmlPage struct {
	T        *testing.T
	Response *http.Response
}

func (hp *HtmlPage) AssertStatusCode(expected int) *HtmlPage {
	code := hp.Response.StatusCode

	if code != expected {
		hp.T.Fatalf("expected %v status code, got: %v", expected, code)
	}

	return hp
}

func (hp *HtmlPage) AssertHeaderValue(key, expected string) *HtmlPage {
	value := hp.Response.Header.Get(key)
	if value == "" {
		hp.T.Fatalf("the header \"%v\" is not set", key)
	}

	if value != expected {
		hp.T.Fatalf("expected header \"%v\" to be: %v, got :%v instead", key, expected, value)
	}

	return hp
}
