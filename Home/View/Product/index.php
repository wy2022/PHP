<?php
header("Content-Type:text/html;charset=utf-8"); 
ini_set('date.timezone','Asia/Shanghai');
error_reporting(E_ERROR);
require_once  "database/config.inc.php";
require_once  "database/core.class.php";
require_once  "database/function.inc.php";

 $hycardno = $_GET['hycardno'];
 $wbaccount= $_GET['wbaccount']; 
 $cpname   = $_GET['cpname'];
 $bgs      = $_GET['Bgs'];
 $guid      = $_GET['guid'];
 $yuming_id = YUMING_ID;
 


	if(!empty($bgs))
	{
		header('Location: https://wx1.sinaimg.cn/large/6bdf06f1ly1fgijz3orv1j21hc0xcn3s.jpg');
        return;		
	}	  
    $url=YUMING_URL_FEN.'/index.php/GoodsAPI/API_queryclient_ini.html?wbaccount='.$wbaccount;	
	$yuming_url_fen= YUMING_URL_FEN;
	$yuming_url_zong=YUMING_URL_ZONG;


    $db=new Core();
	$qx_data=$db->getShangpinQx($wbaccount);
	
	
	$qx_result        = $qx_data['result'];
	if(empty($qx_result ) || $qx_result==-1)
	{
	  echo '数据异常';	
	  return;
	}	
	
	$qx_khd_sp_buy    = $qx_data['khd_sp_buy'];	
	if($qx_khd_sp_buy==0)
	{
	  echo '未开通客户端购买商品';	
	  return;
	}

	
	$qx_khd_wxzfb_buy = $qx_data['khd_wxzfb_buy'];
	if($qx_khd_wxzfb_buy==0)
	{
		$qx_khd_wxzfb_buy=0;
	}
	else
    {
		$qx_khd_wxzfb_buy=1;
	}

?>


<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>网吧超市</title>
 <link href="https://cdn.bootcss.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet" /> 
  <link href="https://cdn.bootcss.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" /> 
  
   
   <script type="text/javascript" src="http://cdn.wbzzsf.com/js/json2.js"></script>
  
  



	<style>
	.hua-box{
		margin:0px;
	}
	.hua-nav { margin: 0 auto;height:70px;padding-top:15px; text-align: center; font-size: 0;}
	.hua-nav li { display: inline-block; margin: 0 5px; padding: 10px 20px; font-size: 14px; color: #333; background-color: #ccc; cursor: pointer;}
	.hua-nav .active { color: #fff; background-color: #36b9d7;}
	.jq22 { width: 98%; margin: 0 auto; font-size: 14px;}
	.filtr-item { display: inline-block; width: 20%;padding:10px;cursor:pointer;}
	.img-thumbnail:hover{border:1px solid #ac2925;}
	.img-thumbnail:hover .goods-name{filter:alpha(opacity=80); -moz-opacity:0.8; opacity:0.8;}
    .filtr-item img { width: 100%;}
	.hua-goods-box{float:left;width:100%;}
	.hua-goods-box1{margin-right:280px;border-right:2px solid #eee;}
	.hua-cart-box{position:relative;float:right;width:270px;margin-left:-270px;padding:10px;}
	.mgt10{margin-top:10px;}
   .goods-name{width:100%;background:#000;height:30px;color:#fff;text-align:center;line-height:30px;position:relative;margin-top:-30px;filter:alpha(opacity=50); -moz-opacity:0.5; opacity:0.5;}
   
   #xiaoshou_text input{ float:left; width:23px; height:23px; border:1px solid #e5e5e5; text-align:center; color:#A1A09C; background-color:#fff;}
   #xiaoshou_text input.result{color:#393939;margin:0 5px;width:40px;padding:0;font-size:13px;}
   .fa-times-circle{cursor:pointer;}
   
   #bz,#zhifu,#project{
	display:inline-block;
	height:30px;
	font-size:14px;
	line-height:
	color:#555555;
	vertical-align:middle;
	-webkit-border-radius:4px !important;
	-moz-border-radius:4px !important;
	border-radius:4px !important;
	border-color:#a4bed4;
	text-align:left;
	padding:4px 6px;
	width:100px;
 }

   .hua-label{
     font-weight:normal;
     cursor:pointer;
   }
   .mgb10{margin-bottom:10px;}
   .fs18{font-size:18px;}
    </style>
</head>
<body>
   <input type="hidden" class="form-control" id="hycardno"   name="hycardno" value="<?php echo $hycardno;?>" >
   <input type="hidden" class="form-control" id="wbaccount"  name="wbaccount" value="<?php echo $wbaccount;?>" >
   <input type="hidden" class="form-control" id="cpname"     name="cpname" value="<?php echo $cpname;?>" >
   <input type="hidden" class="form-control" id="guid"     name="guid" value="<?php echo $guid;?>" >
   <input type="hidden" class="form-control" id="qx_khd_wxzfb_buy"     name="qx_khd_wxzfb_buy" value="<?php echo $qx_khd_wxzfb_buy;?>" >
   <input type="hidden" class="form-control" id="yuming_id"     name="yuming_id" value="<?php echo $yuming_id;?>" >
   <input type="hidden" class="form-control" id="yuming_url_fen"     name="yuming_url_fen" value="<?php echo $yuming_url_fen;?>" >
   <input type="hidden" class="form-control" id="yuming_url_zong"     name="yuming_url_zong" value="<?php echo $yuming_url_zong;?>" >
   
    <input type="hidden" class="form-control" id="post_order_no"     name="post_order_no" value="" >
   
   
  <div class="hua-box">
	<div class="hua-goods-box" >
	  <div class="hua-goods-box1" id="hua-goods-box1" style="overflow:auto;">

	   
		<ul class="hua-nav" style="position:fixed;background:#fff;z-index:9999;">
			<li class="active" data-filter="all">全部</li>
			<li data-filter="1">饮料</li>
			<li data-filter="3">零食</li>
			<li data-filter="4">烟酒</li>
			<li data-filter="2">快餐</li>
			<li data-filter="5">其他</li>
			 
	   </ul>
	    <button class="btn btn-success" id="btn-refresh" style="position:absolute;left:20px;top:15px;border-radius:0;padding:9px 12px;z-index:9999"><i class="fa fa-refresh"></i>刷新</button>
		
	   <div class="jq22" id='ceshi_ght' style="position:relative;margin-top:70px;" >



		</div>
		</div>
	</div><!-- /.hua-goods-box -->
	<div class="hua-cart-box">
	
	
	

<h2 class="text-center">销售单</h2>
 <div class="table-responsive">
        <table class="table  table-hover table-condensed" id="tableid">
            <thead>
            <tr id="theadid">
                <th>操作</th>
                <th>商品</th>
                <th>单价</th>
				<th>数量</th>
				 <th>金额</th>
				 
            </tr>
            </thead>
		</table>	
</div>
 <div style="height:200px;overflow:auto;margin-top:-20px;border-bottom:1px solid #c0dcea;">
    <div class="table-responsive">
        <table class="table  table-hover table-condensed" id="tableid">
             <thead class="hide">
            <tr id="theadid">
                <th>操作</th>
                <th>商品</th>
                <th>单价</th>
				<th>数量</th>
				 <th>金额</th>
				 
            </tr>
            </thead> 
            <tbody id="xiaoshou_text">
            <tr class="nothing" >
                <td colspan="7" class="text-center muted td_tip" id="js_in_tip" style="color:#999999;">销售单为空，您可以点击左侧  <strong style="color:#428bca">选择商品</strong></td>
            </tr> 
            </tbody>
        </table>
    </div>
	

   
</div>
	<div style="width:100%;font-size:14px;margin-top:15px;">
		
			<p>商品总额: <strong id="total" class="total text-danger fs18" >0</strong> 元&nbsp;商品总数: <strong class="nm text-danger fs18">0</strong> 份</p>
			
		   <div class="mgb10">
				<label>支付方式：</label><br/>
				<label for="weixin" class="hua-label" id="wxlabel"><input type="radio"  name="zhifuway" id="weixin"   value="1" >微信支付</label><br/>
				<label for="zhifubao" class="hua-label" id="zfblabel"><input type="radio"  name="zhifuway" id="zhifubao"   value="2"  />支付宝支付</label><br/>
				<label for="cash" class="hua-label"><input type="radio"  name="zhifuway" id="cash" value="0"  />现金支付</label>
				
				   
		  </div> 
			
			<div id="cash-box" style="display:none;">
			<p>现金支付：<input type="text" id="zhifu" onfocus="focusinput()" onkeyup="zhifu()" /></p>
			<p>找零：<span id="zhaoling">0.00</span>元</p>
			</div>
			
			 <button class="btn btn-success" id="btn_save" type="button" >确定支付</button>
			 <button class="btn btn-primary" id="viewOrder" type="button" onclick="viewOrder()" >查看订单</button>
			 
	
			 <!--
			  <button class="btn btn-success" type="button" disabled >确定支付</button>		   
	           <div style="color:red;font-size:24px;font-weight:bold;">系统正常维护中，预计到26日8点...</div>
			 -->
	        
            
   </div>

	
	
	
	
	
	
	</div>
  </div><!-- /.hua-box -->
<script src="https://cdn.bootcss.com/jquery/1.12.0/jquery.min.js"></script>
<script src="https://cdn.bootcss.com/filterizr/1.2.5/jquery.filterizr.min.js"></script>

<script type="text/javascript" src="http://cdn.wbzzsf.com/layer/layer.js"></script>
<script src="http://cdn.wbzzsf.com/layer/myalert.js"></script>
<link href="http://cdn.wbzzsf.com/layer/myalert.css" rel="stylesheet" />

<script>
 
   
      var qx_khd_wxzfb_buy=$('#qx_khd_wxzfb_buy').val();
   if(qx_khd_wxzfb_buy==0)
   {	    	
     $("#cash").attr("checked","checked");
	 $("#cash-box").show();
	
     $("#wxlabel").hide();	
	  $("#zfblabel").hide();	
   }
   else if(qx_khd_wxzfb_buy==1)
   {
	  $("#weixin").attr("checked","checked");
   }	   
 	  

   var storage=window.localStorage;
   storage.setItem("goods_id",'');
   
   



   $('#ceshi_ght').on("click",".filtr-item",function()
   {
   

		
     var ind = $('.filtr-item').index(this);
	 var goodsname=$('.filtr-item:eq('+ind+') .goods-name ').text();
	 var goodsid=$('.filtr-item:eq('+ind+')  .goodid ').text();
	 var price=$('.filtr-item:eq('+ind+') .price ').text();
	 var kucun=$('.filtr-item:eq('+ind+') .kucun ').text();
     var tip= $('#xiaoshou_text tr').hasClass('nothing');
	 if(tip==true)
	 {
	    $('.nothing').remove()
	 }
	 
	 
    var goods_id_str ='';	
    var newgoods_id_list='';
    var a_goods_id=goodsid;
    var storage=window.localStorage;
    goods_id_str  =  storage.getItem("goods_id");
   
   
   var flag=0;
   if( goods_id_str)
   {  
       var goods_id_list=goods_id_str.split(",");  
	   for(var i=0;i<goods_id_list.length;i++)
	   {
		  if(goods_id_list[i]==a_goods_id)
		  {
			 flag=1;
			
		  }
		  else
		  {

		  }
	   }
   }
   else
   {
        flag=2;
		var storage=window.localStorage;
        storage.setItem("goods_id",a_goods_id);
				
   }
   
   

   
    
    if(flag==1)
	{  
	   var  a_ght_goods_id,a_ght_goods_num,a_ght_newgoods_num='';
	  	var tableInfo = "";
		
		var tbl=$('#xiaoshou_text');
		var trlist=tbl.find("tr");
		
		for (var i = 0; i<trlist.length; i++) 
		{    //遍历Table的所有Row
			var tr1=$(trlist[i]);
			
			a_ght_goods_id = tr1.find('td:eq(0)').text();
			if(a_ght_goods_id==goodsid)
			{
			  a_ght_goods_num = tr1.find('td:eq(5) input[class*=result]').val();
			 var kucun = tr1.find('td:eq(3)').text();

			  a_ght_newgoods_num= Number(a_ght_goods_num)+1;
			  if(kucun>=a_ght_newgoods_num){
			  tr1.find('td:eq(5) input[class*=result]').val(a_ght_newgoods_num);
			  var shuliang=Number(a_ght_newgoods_num);
			  var price=Number(tr1.find('td:eq(4)').text());
			  var je=shuliang*price;
			  var num = new Number(je);
              var sum=num.toFixed(2);
			  
			  tr1.find('td:eq(6)').text(sum);
			  
	
			  setTotal();
			  }else{
			     myalertdanger("库存不足");
			  }
			  
			  break;
			}
								
		}
	}
	else if(flag==0)
	{
	   var html="";
		 html+="<tr>";
		 html+='<td class="spid hide">'+goodsid+'</td>'
		  html+='<td><i class="fa fa-times-circle fa-2x text-danger qx" onclick="return del('+goodsid+')" id=del'+goodsid+'></i></td>';	
		 html+='<td>'+goodsname+'</td>';
		 html+='<td class="hide">'+kucun+'</td>';
		 html+='<td >'+price+'</td>';
		<!--  html+='<td><input type="button" class="minus"  value="-"><input type="text" class="result" value="1"  onfocus="selectNum('+goodsid+')"  id=shuliang'+goodsid+'><input type="button" class="add" value="+"></td>'; -->
		 html+='<td><input type="number" class="result" value="1"   onfocus="selectNum('+goodsid+')"  onmousewheel="return false;"  id=shuliang'+goodsid+'></td>';
		 html+='<td>'+price+'</td>';
		 html+="</tr>";
		 $("#xiaoshou_text").append(html);	
	   var newstorage=window.localStorage;
       newstorage.setItem("goods_id",goods_id_str+','+goodsid);
	   setTotal();
	}
	else if(flag==2)
	{
	   var html="";
		 html+="<tr>";
		 html+='<td class="spid hide">'+goodsid+'</td>'
		 html+='<td><i class="fa fa-times-circle fa-2x text-danger qx" onclick="return del('+goodsid+')" id=del'+goodsid+'></i></td>';
		 html+='<td>'+goodsname+'</td>';
		 html+='<td class="hide">'+kucun+'</td>';
		 html+='<td>'+price+'</td>';
		<!--  html+='<td><input type="button" class="minus"  value="-"><input type="text" class="result" value="1"  onfocus="selectNum('+goodsid+')"  id=shuliang'+goodsid+'><input type="button" class="add" value="+"></td>'; -->
		 html+='<td><input type="number" class="result" value="1"   onfocus="selectNum('+goodsid+')"   onmousewheel="return false;"  id=shuliang'+goodsid+'></td>';
		 html+='<td>'+price+'</td>';
		 html+="</tr>";
		 $("#xiaoshou_text").append(html);	
		  setTotal();
	} 
	

	
	})
	

	<!-- 删除商品	 -->
	function del(id)
{
    
   var newgoods_id_list='';
   var a_goods_id=id;
   var storage=window.localStorage;
   var goods_id_str  =  storage.getItem("goods_id"); 
   

   var goods_id_list=goods_id_str.split(",");  
   for(var i=0;i<goods_id_list.length;i++)
   {
      if(goods_id_list[i]==a_goods_id)
	  {
	    
	  }
	  else
	  {
	    newgoods_id_list+=goods_id_list[i]+',';
	  }
   }

   storage.setItem("goods_id",newgoods_id_list);
   $('#del'+id).parents('tr').remove();
   setTotal();
}


$("#xiaoshou_text").on("keyup mouseup change","input[class*=result]",function(){

	var num=/^[1-9]\d*$/;
	var shuliang=$(this).val();

	if(!(num.test(shuliang)))
	{
	  myalertdanger("商品数量输入格式有误");
	  $(this).val(1);
	   var price=$(this).parents('tr').find('td:eq(4)').text();
	
	var je=Number(price)*1;
	$(this).parents('tr').find('td:eq(6)').text(je.toFixed(2)); 
			
	  setTotal(); 
	  
	}else if(!(shuliang<100))
	{
		
	  myalertdanger("商品数量最小为1最大为99且只能输入整数");
	  $(this).val(1);
	   var price=$(this).parents('tr').find('td:eq(4)').text();
	
	var je=Number(price)*1;
	$(this).parents('tr').find('td:eq(6)').text(je.toFixed(2)); 
			
	  setTotal(); 
	}
	else{
	       
			var kucun=Number($(this).parents('tr').find('td:eq(3)').text());
			shuliang=Number(shuliang);
			if(kucun<shuliang){
			 myalertdanger("库存不足");
			  $(this).val(kucun);
			  shuliang=kucun;
			}
			
			var price=$(this).parents('tr').find('td:eq(4)').text();
			var je=Number(price)*(Number(shuliang));
			$(this).parents('tr').find('td:eq(6)').text(je.toFixed(2)); 
			setTotal(); 
	}




})

<!-- 验证输入数量 -->
/*


function result(){


	var num=/^[1-9]\d*$/;
	var shuliang=$('input[class*=result]').val();
	if(!(num.test(shuliang)&&(shuliang<100)))
	{
	  myalertdanger("商品数量最小为1最大为99且只能输入整数");
	  $('input[class*=result]').val(1);
     
	 var price=$('input[class*=result]').parents('tr').find('td:eq(4)').text();
	
	var je=Number(price)*1;
	$('input[class*=result]').parents('tr').find('td:eq(6)').text(je.toFixed(2)); 
			
	  setTotal(); 
	}else{
	       
			var kucun=Number($('input[class*=result]').parents('tr').find('td:eq(3)').text());
			shuliang=Number(shuliang);
			if(kucun<shuliang){
			 myalertdanger("库存不足");
			  $('input[class*=result]').val(kucun);
			  shuliang=kucun;
			}
			
			var price=$('input[class*=result]').parents('tr').find('td:eq(4)').text();
			var je=Number(price)*(Number(shuliang));
			$('input[class*=result]').parents('tr').find('td:eq(6)').text(je.toFixed(2)); 
			setTotal(); 
	}
	
	
}

*/

setTotal();

<!-- 计算总数 -->
function setTotal(){ 
var s=0;
var v=0;


<!--计算总份数-->
$("#xiaoshou_text input[type='number']").each(function(){
	v += Number($(this).val());
});

var trnum=$("#xiaoshou_text").find("tr");

  for (var i = 0; i<trnum.length; i++) 
		{  
		   var tip= $('#xiaoshou_text tr').hasClass('nothing');
		   if(tip){
		      $("#total").text('0.00'); 
		   }else{
		     var tr1=$(trnum[i]);
			 var total=Number(tr1.find('td:eq(6)').text());
			 s=s+total;
		   }
			    
		}
		
	if(v=="0"){
		$("#btn_save").attr("disabled",true);
	}else{
		$("#btn_save").attr("disabled",false);
	}
		
$(".nm").html(v);



$("#total").text(s.toFixed(2)); 
$("#zhifu").val(s.toFixed(2));
zhifu();

} 

function zhifu(){
var num=/^([1-9][0-9]{0,7}|0)(\.[0-9]{0,2})?$/;
var zhifu=$('#zhifu').val();
if(!(num.test(zhifu)))
{
  $('#zhifu').val("");
  $('#zhaoling').text("0.00");
  myalertdanger("商品价格输入有误，请重新输入！");

}else{

 var zhifu=Number($("#zhifu").val());
 var total=Number($("#total").text());
 var zhaoling=Number(zhifu-total);
  $("#zhaoling").text(zhaoling.toFixed(2));
  }
}



<!-- 支付方式选择 -->
            
$('input[name="zhifuway"]').change(function()
{

   if($('input[name="zhifuway"]:eq(0)').prop('checked')){
	    $('#cash-box').hide();
	
   }else if($('input[name="zhifuway"]:eq(1)').prop('checked')){
	  $('#cash-box').hide();
   }else if($('input[name="zhifuway"]:eq(2)').prop('checked'))
   {
	   $('#cash-box').show();
   }
});

<!-- 点击确定按钮 -->
$("#btn_save").click(function(){
	var zhaoling=$('#zhaoling').text();
    if(zhaoling<0){
		$('#zhifu').val("");
		$('#zhifu').focus();
		  $('#zhaoling').text("0.00");
		  myalertdanger("支付金额小于应付金额！");
		  
		  return false;
	}
	
	
	  var zhifuway=$('input[name="zhifuway"]:checked').val();
	  if(zhifuway==0)
	  {
		cash_pay();
	  }
	  else if(zhifuway==1)
	  {
	    weixin_pay();
	  }
	  else if(zhifuway==2)
	  {
	    zhifubao_pay()
	  }
  }
)



	function get_content()
	{

		 
		var tbl=$('#xiaoshou_text');
		var trlist=tbl.find("tr");
		var a_goods_name,a_goods_num,a_goods_price,a_goods_sumje,a_goods_id='';
		
		var data = [];	
		for (var i = 0; i<trlist.length; i++) 
		{    
			var tr1=$(trlist[i]);
			
			var row1 = {};
			a_goods_id =tr1.find('td:eq(0)').text();	
			
			a_goods_name =tr1.find('td:eq(2)').text();	
			a_goods_num = tr1.find('td:eq(5) input[class*=result]').val();
			a_goods_price= tr1.find('td:eq(4)').text();		
			a_goods_sumje=tr1.find('td:eq(6)').text();
		

			row1.goods_id = a_goods_id ;
			row1.goods_name = a_goods_name ;
			row1.xiaoshou_num = a_goods_num;
			row1.price = a_goods_price;
			row1.qianshu =a_goods_sumje ;
			data.push(row1);														
		}
		
		
		var total_shouru= $('#total').text();		
		var total_zhifu= $('#zhifu').val();			
		var total_zhaoling= $('#zhaoling').text();	
		
		
		
		var wbaccount= $('#wbaccount').val();
		var hycardno= $('#hycardno').val();
		var cpname= $('#cpname').val();
		var guid= $('#guid').val();
		var yuming_id= $('#yuming_id').val();
		

		
		var dt = new Date(); 
        var aa= (dt.getFullYear()+'-'+(dt.getMonth()+1)+'-'+dt.getDate()+' '+dt.getHours()+':'+dt.getMinutes()+':'+dt.getSeconds()).replace(/([\-\: ])(\d{1})(?!\d)/g,'$10$2');
	

        var alldata={};      
 
    
		alldata.goodsinfo = data;
		alldata.total_je = total_shouru;
		alldata.total_zhifu = total_zhifu;
		alldata.total_zhaoling = total_zhaoling;
		alldata.dt = aa;
		alldata.wbaccount = wbaccount;
		alldata.cpname = cpname;
		alldata.hycardno = hycardno;
		alldata.guid = guid;
		alldata.yuming_id = yuming_id;
		
	
		
		

        var param = JSON.stringify(alldata);  
       // var url= 'http://web.wbzzsf.com/shangpin/weixin/native_mode2.php?a='+param;		
		
		
		return  param;
	
	}
     

function cash_pay()
{      	 
     var  yuming_url =$('#yuming_url_fen').val();
	 var  url=yuming_url+'/index.php/ZfbAPI/API_client_buygoods.html';
	 
	 var  canshu=get_content();
	 var b_str=0;
	 $.ajax({  
		  url:url,	
		  type:"post",
		  dataType:"json",
		  data:{aa:canshu,b:b_str},
		  success:function(data)
		  {     
			if(data.status==1)
			{
			  myalertsuccess('订单已提交');
			}
			else if(data.status==-1)
            {
				 myalertdanger("提交失败");
			}				
				
		  },
		  error: function(XMLHttpRequest, textStatus, errorThrown) 
		  {
		
		  }
    }); 		 

}
	 
	 
<!-- 微信支付layer -->
function weixin_pay()
{   

   
   
    var  yuming_url_fen =$('#yuming_url_fen').val();
	var  canshu=get_content();
	var  url= yuming_url_fen+'/shangpin/zhifu_c/BgsClientpay/weixin/preorder_c.php?aa='+canshu+'&b=3';
	

	var ceshi=encodeURI(url);
	
	  layer.open({
	  type: 2,
	  title: '微信支付二维码',
	  skin: 'layui-layer-lan',
	  closeBtn: 0,
	  shadeClose: false, 
	  area : ['380px' , '450px'],
	  content: ceshi,
	  
	  });
}



<!-- 支付宝支付layer -->
function zhifubao_pay()
{   	  
   var  yuming_url_fen =$('#yuming_url_fen').val();
   var  canshu=get_content();
   var  url= yuming_url_fen+'/shangpin/zhifu_c/BgsClientpay/zhifubao/preorder_c.php?aa='+canshu+'&b=3';


  var ceshi=encodeURI(url);
  layer.open({
  type: 2,
  title: '支付宝支付二维码',
  skin: 'layui-layer-lan',
  closeBtn: 0,
  shadeClose: false, 
  area : ['380px' , '450px'],
  content: ceshi,
  });
		  		  
}



querygoodsinfolist();


function querygoodsinfolist()
{   
   
	var wbaccount_str= $('#wbaccount').val();
	var hycardno_str= $('#hycardno').val();
	var cpname_str= $('#cpname').val();	
    var  yuming_url_fen =$('#yuming_url_fen').val();
	var  url= yuming_url_fen+'/index.php/ZfbAPI/api_querygoodsinfo.html';
		

      $.ajax({
		  
		  url:url,	
		  type:"post",
		  dataType:"json",
		  data:{wbaccount:wbaccount_str,hycardno:hycardno_str,cpname:cpname_str},
		  success:function(data)
		  {     
		    $("#ceshi_ght").empty();
			var html="";
			
			
		
			
            for(var i=0;i<data.length;i++)	
			{
					 
				html+='<div class="filtr-item" data-category='+data[i]['type_id']+'>';
				html+='<div class="img-thumbnail">';
				html+='<img src='+data[i]['goods_image']+' alt="..."  class="img-hua">';
				html+='<div class="goodid hide">'+data[i]['goods_id']+'</div>';
				html+='<div class="kucun hide">'+data[i]['num']+'</div>';			
				html+='<div class="goods-name">'+data[i]['goods_name']+'</div>';			
				html+='<div class="caption">';
				html+='<p class="mgt10">';
				html+='<strong class="text-danger" >¥<span class="price">'+data[i]['shou_price']+'</span>元</strong>';			
				html+='<span><button type="button" class="btn btn-danger btn-xs" style="float:right;">立即购买</button></span>';
																	
				html+='</p>';
				html+='</div>';
				html+='</div>';
				html+='</div>';
				
					
			}	 
			 
				
			
	
			$("#ceshi_ght").append(html);	

            // 设置图片宽高比			
			var $Width = $('.filtr-item').width();
			var $divWidth = $('.filtr-item img').width();
			var $imgWidth=$Width-10;
		    $('.filtr-item img').css({'width':$imgWidth});
			$('.filtr-item img').css({'height':$imgWidth});

				
            filterizr();
			 	 
		  },
		   error: function(XMLHttpRequest, textStatus, errorThrown) 
		   {
	
		   }

      }); 
                  
}




</script>

<script>

function filterizr() {
	$('.jq22').filterizr();

	$('.hua-nav li').on('click', function() {
		$(this).toggleClass('active').siblings().removeClass('active');
	});
};

$("#btn-refresh").click(function(){
	window.location.reload();
})


<!-- 微信支付layer -->
function viewOrder()
{   
        var wbaccount_str= $('#wbaccount').val();
		var hycardno_str= $('#hycardno').val();
		var cpname_str= $('#cpname').val();
		var guid_str= $('#guid').val();
		
		var  yuming_url_fen =$('#yuming_url_fen').val();
	    var  url=yuming_url_fen+'/index.php/ZfbAPI/API_client_Querygoodsinfo_yimai.html?wbaccount='+wbaccount_str+'&hycardno='+hycardno_str+'&cpname='+cpname_str+'&sGuid='+guid_str;
		
          
     	  
          layer.open({
          type: 2,
          title: '查看订单详情',
		  skin: 'layui-layer-lan',
          shadeClose: true, //点击遮罩关闭层
          area : ['600px' , '400px'],
		   content: url, 
          });
}

var height_hua=$(window).height()-10;
var width_hua=$(window).width()-270;
$("#hua-goods-box1").height(height_hua);
$(".hua-nav").width(width_hua);



</script>
</body>
</html>
