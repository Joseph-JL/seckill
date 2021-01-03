package main

import (
	//HTTP请求包
	"encoding/json"
	"fmt"
	"io"

	//"log"
	"net/http"

	//RabbitMQ包
	"log"

	"github.com/streadway/amqp"
)

//接收到的请求Form数据
type Massage struct {
	Username string `json:"Username,string"`
}

//Ret信息
type Ret struct {
	Code int    `json:"code,int"`
	Data string `json:"data"`
}

//one 返回给浏览器信息
func printRequest(w http.ResponseWriter, r *http.Request) {
	//1.从浏览器接收表单数据(Get参数)，在本地打印(需转化为json再送到rabbitMQ
	fmt.Println("r.Form=", r.Form)
	massage := Massage{r.Form.Get("username")}
	fmt.Println(massage)

	//2.返回给浏览器的回复，在浏览器打印
	ret := new(Ret)
	ret.Code = 200
	ret.Data = "提交成功"
	w.Header().Set("Content-Type", "application/json; charset=utf-8")
	retJSON, _ := json.Marshal(ret) //转化为json格式
	io.WriteString(w, string(retJSON))

}

//two 订单生成，扔到RabbitMQ队列
//检查每一个amqp调用的返回值
func failOnError(err error, msg string) {
	if err != nil {
		log.Fatalf("%s: %s", msg, err)
	}
}

//订单生成，扔到RabbitMQ队列
func OrderSend(w http.ResponseWriter, r *http.Request) {

	//浏览器Get请求转化存到massage里面
	massage := Massage{r.Form.Get("username")}
	fmt.Println(massage)
	//请求转化为json格式
	massageJSON, err := json.Marshal(massage)
	failOnError(err, "Failed to encode the message to json")

	//连接RabbitMQ服务器
	conn, err := amqp.Dial("amqp://admin:admin@101.200.78.125:5672/")
	failOnError(err, "Failed to connect to RabbitMQ")
	defer conn.Close()

	ch, err := conn.Channel()
	failOnError(err, "Failed to open a channel")
	defer ch.Close()

	//为了发送消息，声明一个队列
	q, err := ch.QueueDeclare(
		"hello", // name
		false,   // durable
		false,   // delete when unused
		false,   // exclusive
		false,   // no-wait
		nil,     // arguments
	)
	failOnError(err, "Failed to declare a queue")

	//发布消息
	body := massageJSON
	err = ch.Publish(
		"",     // exchange
		q.Name, // routing key
		false,  // mandatory
		false,  // immediate
		amqp.Publishing{
			ContentType: "text/plain",
			Body:        []byte(body),
		})
	log.Printf(" [x] Sent %s", body)
	failOnError(err, "Failed to publish a message")
}

func sayMore(w http.ResponseWriter, r *http.Request) {
	r.ParseForm()      //解析参数，默认是不会解析的
	printRequest(w, r) //返回给浏览器信息
	OrderSend(w, r)    //request转化为消息并抛到RabbitMQ

}

func main() {
	http.HandleFunc("/api/room/order", sayMore) //设置访问的路径
	err := http.ListenAndServe(":10000", nil)    //设置监听的端口
	if err != nil {
		log.Fatal("ListenAndServe: ", err)
	}
}
