package main

import (
	"fmt"
	"time"
)

const (
	TotalTime = 12 * 60 * 60
	FDateTime = "2006-01-02 15:04:05" // 奇葩的时间点，格式化必须是这个格式……
	FDate     = "2006-01-02"
	FTime     = "15:04:05"
	StartTime = "2018-01-01 00:00:00" // 我们设置的起点时间
)

func main() {
	fmt.Println(time.Now().Format(FDate)) // 先试试格式化日期
	fmt.Println(time.Now().Format(FTime)) // 再试试格式化时间
	// 下面定个时间起点
	fromDate, _ := time.ParseInLocation(FDateTime, StartTime, time.Local)

	for i := 0; i <= TotalTime; i++ {
		hourNeedle := (i * 1) % TotalTime
		minuteNeedle := (i * 12) % TotalTime
		secondNeedle := (i * 720) % TotalTime

		if hourNeedle == minuteNeedle && minuteNeedle == secondNeedle {
			fmt.Println("Three needles get together!")
			fmt.Println(time.Unix(fromDate.Unix()+int64(i), 0).Format(FTime))
		}
	}
}
