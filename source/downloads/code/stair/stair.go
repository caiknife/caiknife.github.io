package main

import "fmt"

func main() {
	// 默认走10阶
	fmt.Println(GoStair(10))
}

func GoStair(s int) int {
	if s < 1 {
		return 0
	}

	if s < 3 {
		return s
	}

	return GoStair(s-1) + GoStair(s-2)
}
