package cde

import (
	"strconv"
	"strings"
)

type CDEimg struct {
	Path, Pre, Domain string
	Start, Domainnum  int
}

const C_str = "0123456789abcdefghijklmnopqrstuvwxyz._-=+[],;#@~"

func SubString(str string, begin, length int) (substr string) {
	// 将字符串的转换成[]rune
	rs := []rune(str)
	lth := len(rs)

	// 简单的越界判断
	if begin < 0 {
		begin = lth + begin
	}
	if begin >= lth {
		begin = lth
	}
	end := begin + length
	if end > lth {
		end = lth
	}

	// 返回子串
	return string(rs[begin:end])
}
func (cdeimg *CDEimg) GetImgDomain(path string) string {
	if len(path) < 5 {
		return ""
	} else {
		//if cdeimg.Domainnum > 36 {
		//	cdeimg.Domainnum = 36
		//}
		//var t_char, c, r string = "", "", ""
		//t_char = "3"
		//c = "23"
		r := strings.Index(C_str, strings.ToLower(SubString(path, cdeimg.Start, 1))) % cdeimg.Domainnum
		//缩写
		return cdeimg.Pre + strconv.Itoa(r) + cdeimg.Domain
	}

}
func (cdeimg *CDEimg) GetImgDomainHttp(path string) string {
	if path == "" {
		return ""
	}

	return "http://" + cdeimg.GetImgDomain(path) + path
}

/**
 * get single img http full url.
 * path begin with /
 * pre为空使用cdeimg中的
 * domain为空使用cdeimg中的
 */
func (cdeimg *CDEimg) GetImgHttp(path string) string {
	if path == "" {
		path = cdeimg.Path
	}
	return "http://" + cdeimg.Pre + cdeimg.Domain + path //$path以/开头

}
