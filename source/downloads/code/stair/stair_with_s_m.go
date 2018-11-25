package main

import "fmt"

func main() {
	// 默认走10阶，每次最多2阶
	fmt.Println(GoStairWithSM(3, 4))
}

func GoStairWithSM(s int, m int) int {
	if s == 0 {
		return 0
	}

	if s == 1 {
		return 1
	}

	result := 0

	if s > m {
		for i := 1; i <= m; i++ {
			result += GoStairWithSM(s-i, m)
		}
	} else {
		result = GoStairWithSM(s, s-1) + 1
	}

	return result
}
