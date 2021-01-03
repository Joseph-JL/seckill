# src
1. TP5_Client_Admin 里面是Thinkphp代码，实现了秒杀系统“用户端”和管理端“功能”;

2. orderProduce_orderProcess 里面是go语言实现的

   “生成订单消息，扔入RabbitMQ消息队列orderProduce1.go”程序

   和“从RabbitMQ消息队列中取出消息，处理订单orderProduce1.go“程序；

3. SQL 里面是数据库seckill导出的SQL语句。