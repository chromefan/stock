<?php
/**
 * 抓站DB类
 * @author luohj
 *
 */
class db {

    private $conn;
    private $querystr;

    function __construct($config=array()){
        $this->conn = mysql_connect("localhost","root","Luohj@2007") or die(mysql_error());
        mysql_select_db("stock",$this->conn);
        //mysql_query("SET NAMES '" . $config['DB_CHARSET'] . "'");
    }

    function query($sql){

        $rs= mysql_query($sql,$this->conn);
        $this->querystr=$sql;
        if ($rs) {
            $data=array();
            while($row=mysql_fetch_assoc($rs)){
                $data[] = $row;
            }
            return $data;
        }else{
            return false;
        }
    }
}
$db = new db();
$sql ="select * from stock limit 1";
$data = $db->query($sql);
var_dump($data);
exit;
/** 
 * 足球彩票2串1拆票算法 
 *  @author luohj *
 *	2014-5-14
 */
define('P',2);


//获取用户投注信息
$c=array(
		'001'=>array(1.8,3.4,3.65),
		'002'=>array(4.05,3.2,1.77),
		'003'=>array(2.28,3.15,2.72)
	);
$c_json=json_encode($c);//json格式用户投注信息
print_r($c_json);

$n=1;//默认倍数为1倍
$b=0;//2串1注数
foreach($c as $v){
	$c_arr[]=$v;//转换为索引规则的数组
}
//计算注数  不管有多少场都能计算出来
for($i=0;$i<count($c_arr);$i++){
	for($j=$i+1;$j<count($c_arr);$j++){
		$b+=count($c_arr[$i])*count($c_arr[$j]);
	}
}
//拆票
for($i=0;$i<count($c_arr);$i++){
	for($j=$i+1;$j<count($c_arr);$j++){
		$b+=count($c_arr[$i])*count($c_arr[$j]);
		$t[]=A($c_arr[$i],$c_arr[$j]);//包含每一张票每一注金额
	}
}
print_r($t);
//最高奖金
foreach($t as $v){
	$max_bonus+=max($v);
}
var_dump("注数 :".$b);
var_dump("总共花费 :".$b*2);
var_dump("最高奖金为 :".$max_bonus);
//专门计算一场比赛的每一注奖金
function A($a,$b){
	for($i=0;$i<count($a);$i++){
		for($j=0;$j<count($b);$j++){
			$result[]=P*$a[$i]*$b[$j];
		}
	}
	return $result;
}

?>
