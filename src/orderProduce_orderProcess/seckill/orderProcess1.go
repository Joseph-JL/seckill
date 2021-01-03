package main

import (
	"encoding/json"
	"errors"
	"time"

	//"errors"
	"fmt"

	"gorm.io/driver/mysql"
	"gorm.io/gorm"

	//"log"
	//"strconv"

	//RabbitMQ包
	"log"

	"github.com/streadway/amqp"
)

//ClientUser
type ClientUser struct {
	ID       int    `gorm:"primaryKey"`
	Username string `gorm:"column:username"`
	Password string `gorm:"column:password"`
	Email    string `gorm:"column:email"`
}

//GoodsClass
type GoodsClass struct {
	ID             int    `gorm:"primaryKey"`
	GoodsName      string `gorm:"column:goodsName"`
	TotalNum       int    `gorm:"column:totalNum"`
	DistributedNum int    `gorm:"column:distributedNum"`
	RemainedNum    int    `gorm:"column:remainedNum"`
}

//GoodsDetail
type GoodsDetail struct {
	ID            int    `gorm:"primaryKey"`
	GoodsNo       int    `gorm:"column:goodsNo"`
	GoodsClassID  int    `gorm:"column:goodsClassID"`
	IsDistributed int    `gorm:"column:isDistributed"`
	ClientName    string `gorm:"column:clientName"`
}

//OrderRecord 订单处理记录：
type OrderRecord struct {
	ID           int    `gorm:"primaryKey"`
	GoodsClassID int    `gorm:"column:goodsClassID"`
	GoodsNo      int    `gorm:"column:goodsNo"`
	ClientName   string `gorm:"column:clientName"`
	FinishTime   string `gorm:"column:finishTime"`
}

//解决表插入表名自动加s问题
func (OrderRecord) TableName() string {
	return "order_record"
}

//接收到的请求Form数据
type Massage struct {
	Username string `json:"Username,String"`
}



//从RabbitMQ中拿消息
func failOnError(err error, msg string) {
	if err != nil {
		log.Fatalf("%s: %s", msg, err)
	}
}
func TakeMassage() {
	conn, err := amqp.Dial("amqp://admin:admin@101.200.78.125:5672/")
	failOnError(err, "Failed to connect to RabbitMQ")
	defer conn.Close()

	ch, err := conn.Channel()
	failOnError(err, "Failed to open a channel")
	defer ch.Close()

	q, err := ch.QueueDeclare(
		"hello", // name
		false,   // durable
		false,   // delete when unused
		false,   // exclusive
		false,   // no-wait
		nil,     // arguments
	)
	failOnError(err, "Failed to declare a queue")

	msgs, err := ch.Consume(
		q.Name, // queue
		"",     // consumer
		true,   // auto-ack
		false,  // exclusive
		false,  // no-local
		false,  // no-wait
		nil,    // args
	)
	failOnError(err, "Failed to register a consumer")

	forever := make(chan bool)

	go func() {
		for d := range msgs {
			log.Printf("Received a message: %s", d.Body)
			//消息接收后交给订单处理程序
			massage := Massage{}
			err = json.Unmarshal(d.Body, &massage)
			failOnError(err, "Failed to decod json")
			OrderProcess(massage)

		}
	}()

	log.Printf(" [*] Waiting for messages. To exit press CTRL+C")
	<-forever
}

//订单处理程序
func OrderProcess(massage Massage) {

	//string转int——————int, err := strconv.Atoi(string)
	//username,_:=strconv.Atoi(massage.Username)//json传递过来的信息
	//var username string = massage.Username
	username1 := massage.Username
	username := username1[1 : len(username1)-1]
	fmt.Println(username)

	dsn := "seckill:liu123456@tcp(120.53.251.22:3306)/seckill?charset=utf8mb4&parseTime&loc=Local" //数据库名+数据库密码
	//连接数据库
	db, err := gorm.Open(mysql.Open(dsn), &gorm.Config{})
	if err != nil {
		fmt.Println(err)
		return
	}
	fmt.Println("------连接数据库成功————————————")

	//结构体和数据库表链接
	db.Table("client_user").AutoMigrate(&ClientUser{})
	db.Table("goods_class").AutoMigrate(&GoodsClass{})
	db.Table("goods_detail").AutoMigrate(&GoodsDetail{})
	db.Table("order_record").AutoMigrate(&OrderRecord{})

	//<----验证---->
	//<-在表client_user中验证该client合法存在->
	//<-在表goods_detail中验证该用户不曾持有该商品->
	//<-商品仍有剩余->
	isok1 := 0 //用户判断验证结果
	isok2 := 0
	isok3 := 0

	//在表client_user中验证该client合法存在
	clientuser := ClientUser{}
	errClientuser := db.Table("client_user").Where("username=?", username).First(&clientuser).Error
	if errors.Is(errClientuser, gorm.ErrRecordNotFound) {
		fmt.Printf("用户%s不存在\n",username) //该用户不存在
	} else {
		isok1 = 1
		fmt.Println(clientuser.Username + "用户存在")
	}

	//在表goods_detail中验证该用户不曾持有该商品
	goodsdetail := GoodsDetail{}
	errGoodsdetail := db.Table("goods_detail").Where("clientName=?", username).First(&goodsdetail).Error
	if errors.Is(errGoodsdetail, gorm.ErrRecordNotFound) {
		isok2 = 1
		fmt.Printf("用户%s没有该商品,可以参与抢购\n",username)
	} else {
		fmt.Printf("用户%s已持有该商品,不能再次抢购\n",username) //该用户已持有该商品,不能再次抢购
	}

	//商品仍有剩余
	goodsclass := GoodsClass{}
	errGoodsClass := db.Table("goods_class").Where("id=1").First(&goodsclass).Error
	if errors.Is(errGoodsClass, gorm.ErrRecordNotFound) {
		fmt.Print("该商品不存在") //该商品不存在
	}else{
		if goodsclass.RemainedNum <= 1000 {
			isok3 = 1 //商品仍有剩余
			fmt.Println("商品" + goodsclass.GoodsName + "有剩余,可以抢购")
		} else {
			fmt.Print("该商品已秒杀完，无剩余")
		}
	}

	//<----分配---->
	//<-商品仍有剩余，在表goods_detail中分配，表goods_class商品数量做相应处理->
	//<-订单处理完成，记录到order_record表->

	//事务开始
	tx := db.Begin()
	if isok1 == 1 && isok2 == 1 && isok3 == 1 { //验证通过
		//商品仍有剩余,在表goods_detail中分配，表goods_class商品数量做相应处理
		goodsdetailone := GoodsDetail{}
		errGoodsdetailone := tx.Table("goods_detail").Where("goodsClassID=1").Where("isDistributed=0").First(&goodsdetailone).Error
		if errors.Is(errGoodsdetailone, gorm.ErrRecordNotFound) {
			fmt.Print("该商品已秒杀完,无剩余") //该商品不存在
		} else {
			//更新goods_detail表
			err1:=tx.Table("goods_detail").Model(&goodsdetailone).Updates(map[string]interface{}{"isDistributed": 1, "clientName": username}).Error
			if err1 != nil {
				tx.Rollback()
				fmt.Println("秒杀失败", err1)
				return
			}

			//更新goods_class表
			goodsclassone := GoodsClass{}
			errGoodsclassone := tx.Table("goods_class").Where("id=1").First(&goodsclassone).Error
			if errors.Is(errGoodsclassone, gorm.ErrRecordNotFound) {
				fmt.Print("该商品已秒杀完,无剩余") //该商品已经秒杀完
			} else {
				distributedNum1 := goodsclassone.DistributedNum + 1
				remainedNum1 := goodsclassone.RemainedNum - 1
				err2:=tx.Table("goods_class").Model(&goodsclassone).Updates(map[string]interface{}{"distributedNum": distributedNum1, "remainedNum": remainedNum1}).Error
				if err2 != nil {
					tx.Rollback()
					fmt.Println("秒杀失败", err2)
					return
				}
			}

			//订单处理完成，记录到order_record表
			orderrecord := OrderRecord{}
			orderrecord.GoodsClassID = 1
			orderrecord.GoodsNo = goodsdetailone.GoodsNo
			orderrecord.ClientName = username
			orderrecord.FinishTime = time.Now().Format("2006-01-02 15:04:05")
			err3 := tx.Create(&orderrecord).Error
			if err3 != nil {
				fmt.Println("商品秒杀失败", err)
				return
			}else{
				tx.Commit()
				fmt.Println("商品秒杀成功") //商品秒杀成功
			}
		}
	} else {
		fmt.Println("商品秒杀失败") //商品秒杀失败
	}


}
func main() {
	TakeMassage()
}
