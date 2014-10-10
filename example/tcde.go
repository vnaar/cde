package main

import (
	"3cde.com/cde"
	"fmt"
)

//初始化
func CdeImg(imgpath string) cde.CDEimg {
	cstruct := cde.CDEimg{}
	cstruct.Path = imgpath
	cstruct.Pre = "img"
	cstruct.Domain = ".3cde.com"
	cstruct.Start = -5
	cstruct.Domainnum = 5

	return cstruct
}
func main() {
	path := "/dada1.jpg"
	cstruct := CdeImg(path)

	fmt.Println(cstruct.GetImgDomain(path))
	fmt.Println(cstruct.GetImgHttp(path))
	fmt.Println(cstruct.GetImgDomainHttp(path))
	fmt.Println("------ok------------")

	path = "/dada2.jpg"
	cstruct = CdeImg(path)

	fmt.Println(cstruct.GetImgDomain(path))
	fmt.Println(cstruct.GetImgHttp(path))
	fmt.Println(cstruct.GetImgDomainHttp(path))
	fmt.Println("------ok------------")

	path = "/dada3.jpg"
	cstruct = CdeImg(path)

	fmt.Println(cstruct.GetImgDomain(path))
	fmt.Println(cstruct.GetImgHttp(path))
	fmt.Println(cstruct.GetImgDomainHttp(path))
	fmt.Println("------ok------------")
}
