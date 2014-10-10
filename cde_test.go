package cde

import (
	"3cde.com/cde"
	"testing"
)

func TestGetImgDomain(t *testing.T) {
	cstruct := &cde.CDEimg{}
	cstruct.Pre = "img02"

	cstruct.Pre = "img"
	cstruct.Domain = ".3cde.com"
	cstruct.Start = -5
	cstruct.Domainnum = 5
	username := cstruct.GetImgDomain("/patta.jpg")
	if username != "img02" {
		t.Error("getAdmin get data error")
	}
}
