package main

import "fmt"

func main() {
	// 默认走10阶
	fmt.Println(GoStairWithLoop(10))
}

func GoStairWithLoop(s int) int {
	// f(1) = 1
	// f(2) = 2
	// result是f(1)的值 tmp是f(2)-f(1)的值
	result, tmp := 1, 1

	for ; s != 0; {
		tmp = result + tmp
		result = tmp - result
		s--
	}

	return result
}
