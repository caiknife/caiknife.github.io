package main

import "fmt"

func main() {
	// 默认走10阶
	fmt.Println(GoStairWithTail(10, 1, 1))
}

func GoStairWithTail(s int, result int, tmp int) int {
	if s < 1 {
		return result
	}

	return GoStairWithTail(s-1, tmp, result+tmp)
}
