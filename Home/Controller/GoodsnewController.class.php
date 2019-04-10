<?php
namespace Home\Controller;
class GoodsnewController extends CommonController {

    public function check_newcs_qx()
    {
        //判断新超市权限
        $exe_sp_version=D('Webini')->where(array('wbid'=>session('wbid'),'skey'=>'exe_sp_version'))->getField('svalue');
        if($exe_sp_version==1)
        {
            $exe_sp_version=1;
            return true;
        }
        else
        {
            $exe_sp_version=0;
            return false;
        }

    }
    public function index()
    {
        /*
        $bOpen=$this->check_newcs_qx();
        if($bOpen===false)
        {
          $this->redirect('rate/fujia', array('cate_id' => 2), 1, '请在附加设置里开启新超市权限...');
        }
        */

        $typelist=D('ProductType')->select();
        $this->assign('typelist',$typelist);

        $map = array();
        $map['wbid']=session('wbid');
        $map['is_zuhe']=array('neq',1);
        $map['deleted']=0;
        $sumnum=D('Newproduct')->where($map)->count();

        $map1=array();
        $map1['is_zuhe']=2;
        $map1['deleted']=0;
        $map1['wbid']=session('wbid');
        $map1['childgoodsnum']=0;
        $zuhelist_no_goods_count=D('Newproduct')->where($map1)->count();
        $sumnum=$sumnum-$zuhelist_no_goods_count;


        $this->assign('sumnum',$sumnum);

        $this->display();
    }
    public function getallshangpinlist()
    {
        if(IS_AJAX)
        {
            $page = I('get.page',1,'int');
            $rows = I('get.rows',10,'int');
            $sord = I('get.sord','','string')?:'desc';
            $sidx = I('get.sidx','','string')?:'dtInsertTime';

            $goods_name    = I('get.goods_name','','string');
            $goods_type      = I('get.goods_type','','string');
            $goods_barcode      = I('get.goods_barcode','','string');

            $map = array();
            $map['sp.wbid']=session('wbid');
            $map['sp.is_zuhe']=array('neq',1);
            $map['sp.deleted']=0;
          //  $map['sp.childgoodsnum']=array('gt',0);;

            if(!empty($goods_name))
            {
                $map['sp.goods_name']=array('LIKE','%'.$goods_name.'%');
            }

            if(!empty($goods_type))
            {
                $map['sp.type_id']=$goods_type;
            }

            if(!empty($goods_barcode))
            {
                $map['sp.barcode']=array('LIKE','%'.$goods_barcode.'%');
            }



            $count= D('Newproduct')->getProductinfoListByMap_count($map);


            $sql_page=ceil($count/$rows);
            if($page>$sql_page) $page=1;
            $wblist = D('Newproduct')->getProductinfoListByMap($map,"$sidx $sord",$page,$rows);

            // print_r(D('Newproduct')->getLastSql());
            // return;

            $response = new \stdClass();
            $response->total = $wblist['count'];
            $response->page = $page;
            $response->rows = $rows;
            $response->list = $wblist['list'];

            $this->ajaxReturn($response);
        }
    }
    public function getkcinfo()
    {
        if(IS_AJAX)
        {
            $page = I('get.page',1,'int');
            $rows = I('get.rows',15,'int');
            $sord = I('get.sord','','string')?:'desc';
            $sidx = I('get.sidx','','string')?:'id';
            $map = array();
            $map['wbid']=session('wbid');
            $type_id = I('get.type_id','','string');
            $sContent = I('get.sContent','','string');




            if(!empty($sContent ))
            {
                $map['goods_name|barcode']= array('LIKE',"%$sContent%");
            }

            if(!empty($type_id ))
            {
                $map['type_id']= $type_id;
            }

            $count=D('Newproduct')->getProductinfoListByMap_count($map);

            $rows=15;
            $sql_page=ceil($count/$rows);

            if($page<=0)   $page=1;
            if($page>$sql_page)
            {
                $page=1;
            }else
            {

            }

            $kctjdata = D('Newproduct')->getProductinfoListByMap($map,"$sidx $sord",$page,$rows);
            $count     = $kctjdata['count'];
            $response = new \stdClass();
            $response->count       = $kctjdata['count'];//返回的数组的第一个字段记录总条数
            $response->nowPage     = $page ;              //每页显示的记录数目
            $response->total       = ceil($kctjdata['count'] / $rows);


            $response->rows   = $kctjdata['list'] ;
            $this->ajaxReturn($response);
        }

    }

    //================================进货明细==========================
    public function jhtj()
    {
        $bOpen=$this->check_newcs_qx();
        if($bOpen===false)
        {
            $this->redirect('rate/fujia', array('cate_id' => 2), 1, '请在附加设置里开启新超市权限...');
        }
        $wbid=session('wbid');
        $yglist=D('Yuangong')->field('id,name')->where(array('WB_ID'=>$wbid))->select();
        $this->assign('yglist',$yglist);
        $this->display();
    }
    public function getjhtjmx()
    {
        if(IS_AJAX)
        {
            $page = I('get.page',1,'int');
            $rows = I('get.rows',15,'int');
            $sord = I('get.sord','','string')?:'desc';
            $sidx = I('get.sidx','','string')?:'id';

            $daterange = I('get.daterange','','string');//获取交班时间
            $shangxiatype =    I('get.shangxiatype','0','string');
            $sContent =    I('get.sContent','','string');
            $operate =     I('get.operate','','string');


            $wbid= session('wbid');
            if(empty($wbid))
            {
                $wbid =-1;
            }
            $map = array();
            if(!empty($wbid))
            {
                $map['wbid']= $wbid;
            }


            if(!empty($shangxiatype))
            {
                if($shangxiatype==1)
                {
                    $map['shangxia_status']=0;
                }else if($shangxiatype==2)
                {
                    $map['shangxia_status']=1;
                }
            }

            if(!empty($operate ))
            {
                $name=D('Yuangong')->where(array('WB_ID'=>session('wbid'),'id'=>$operate))->getField('name');
                $map['operate']= array('LIKE',"%$name%");
            }

            if(!empty($sContent))
            {
                $map['goods_name']=array('LIKE',"%$sContent%");
            }


            if(!empty($daterange))
            {
                list($start,$end) = explode(' - ',$daterange);
                $start = str_replace('/','-',$start);
                $end = str_replace('/','-',$end);
                $map['dtInsertTime']=array('BETWEEN',array($start,$end));
            }

            $count=D('Newproductsxj')->getsxjinfoListByMap_count($map);

            $rows=15;
            $sql_page=ceil($count/$rows);

            if($page<=0)   $page=1;
            if($page>$sql_page)
            {
                $page=1;
            }
            else
            {

            }

            $jhtjdata = D('Newproductsxj')->getsxjinfoListByMap($map,"$sidx $sord",$page,$rows);

            $count    = $jhtjdata['count'];

            $response = new \stdClass();
            $response->count       = $jhtjdata['count'];//返回的数组的第一个字段记录总条数
            $response->nowPage     = $page ;              //每页显示的记录数目
            $response->total       = ceil($jhtjdata['count'] / $rows);

            $response->rows          = $jhtjdata['list'] ;
            $response->tongji_list   = $jhtjdata['tongji_list'] ;
            $response->tongji_count   = $jhtjdata['tongji_count'] ;
            $this->ajaxReturn($response);
        }

    }
    //================================进货明细==========================
    //==============================销售明细===========================
    public function xstj()
    {
        $bOpen=$this->check_newcs_qx();
        if($bOpen===false)
        {
            $this->redirect('rate/fujia', array('cate_id' => 2), 1, '请在附加设置里开启新超市权限...');
        }
        $wbid= session('wbid');
        $yglist=D('Yuangong')->field('id,name')->where(array('WB_ID'=>$wbid))->select();
        $this->assign('yglist',$yglist);
        $this->display();
    }
    public function getxstjmx()
    {
        if(IS_AJAX)
        {
            $page = I('get.page',1,'int');
            $rows = I('get.rows',20,'int');
            $sord = I('get.sord','','string')?:'desc';
            $sidx = I('get.sidx','','string')?:'id';

            $sContent = I('get.sContent','','string');
            $daterange = I('get.daterange','','string');
            $ordertype = I('get.ordertype','','string');
            $paytype = I('get.paytype','','string');
            $operate = I('get.operate','','string');


            $wbid= session('wbid');
            if(empty($wbid))
            {
                $wbid =-1;
            }

            $map = array();
            if(!empty($wbid))
            {
                $map['wbid']= session('wbid');
            }

            if(!empty($ordertype))
            {
                $map['ordertype']=$ordertype ;
            }

            if(!empty($operate ))
            {
                $name=D('Yuangong')->where(array('WB_ID'=>session('wbid'),'id'=>$operate))->getField('name');
                $map['operate']= array('LIKE',"%$name%");
            }

            if(!empty($paytype))
            {
                $map['paytype']=$paytype ;
            }


            if(!empty($sContent ))
            {
                $map['info|post_order_no']= array('LIKE',"%$sContent%");
            }

            if(!empty($daterange))
            {
                list($start,$end) = explode(' - ',$daterange);
                $start = str_replace('/','-',$start);
                $end = str_replace('/','-',$end);
                $map['dtInsertTime']=array('BETWEEN',array($start,$end));
            }


            $count=D('Newproductxs')->getxstjlistByMap_count($map);
            $rows=10;
            $sql_page=ceil($count/$rows);

            if($page<=0)   $page=1;
            if($page>$sql_page)
            {
                $page=1;
            }else
            {

            }

            $spxsdata = D('Newproductxs')->getxstjlistByMap($map,"$sidx $sord",$page,$rows);
            $count     = $spxsdata['count'];


            $response = new \stdClass();
            $response->count       = $spxsdata['count'];//返回的数组的第一个字段记录总条数
            $response->nowPage     = $page ;              //每页显示的记录数目
            $response->total       = ceil($spxsdata['count'] / $rows);

            $sumje=0;
            $sum_nocash_je=0;
            $sum_cash_je=0;
            $sum_other_je=0;

            $sum_xs_num=0;
            $sum_jbxs_num=0;
            $sum_th_num=0;
            foreach($spxsdata['list'] as &$val)
            {
                if($val['paytype']==1)
                {
                    $sum_cash_je+=$val['sum_sr_je'];
                }
                else  if($val['paytype']==2)
                {
                    $sum_nocash_je+=$val['sum_sr_je'];
                }
                else  if($val['paytype']==3)
                {
                    $sum_other_je+=$val['sum_sr_je'];
                }

                if($val['ordertype']==1)
                {
                    $sum_xs_num+=$val['sum_num'];
                }
                else  if($val['ordertype']==2)
                {
                    $sum_jbxs_num+=$val['sum_num'];
                }
                else  if($val['ordertype']==3)
                {
                    $sum_th_num+=$val['sum_num'];
                }

            }
            $sumje= $sum_nocash_je+$sum_cash_je+$sum_other_je;


            $response->rows   = $spxsdata['list'] ;

            $response->sumje   = $sumje ;
            $response->sum_cash_je   = $sum_cash_je ;
            $response->sum_nocash_je   = $sum_nocash_je ;
            $response->sum_other_je   = $sum_other_je ;
            $response->sum_xs_num   = $sum_xs_num ;
            $response->sum_th_num   = $sum_th_num;
            $response->sum_jbxs_num   = $sum_jbxs_num ;
            $this->ajaxReturn($response);
        }
    }
    public function xq_xiaoshoumx()
    {
        $bOpen=$this->check_newcs_qx();
        if($bOpen===false)
        {
           // $this->redirect('rate/fujia', array('cate_id' => 2), 1, '请在附加设置里开启新超市权限...');
        }
        $post_order_no=I('get.post_order_no','','string');
        session('post_order_no',$post_order_no);
        $this->display();
    }

    public function getxs_mx_listByMap()
    {

        if(IS_AJAX)
        {
            $page = I('get.page',1,'int');
            $rows = I('get.rows',20,'int');
            $sord = I('get.sord','','string')?:'desc';
            $sidx = I('get.sidx','','string')?:'dtInsertTime';


            $map = array();
            $map['wbid']=session('wbid');
            $map['post_order_no']= session('post_order_no');
         

            $count= D('Newproductxsmx')->getxstongji_mx_listByMap_count($map);

            $sql_page=ceil($count/$rows);
            if($page>$sql_page) $page=1;
            $wblist = D('Newproductxsmx')->getxstongji_mx_listByMap($map,"$sidx $sord",$page,$rows);
  
            $response = new \stdClass();
            $response->records = $wblist['count'];
            $response->page = $page;
            $response->total = ceil($wblist['count'] / $rows);
            foreach($wblist['list'] as $key => $value)
            {
                $response->rows[$key]['id'] = $key;
                $response->rows[$key]['cell'] = $value;
            }
            $this->ajaxReturn($response);
        }
      
    }
   



    
    //-------------------------交班明细--------------------------------
    
    public function getjbtjlist()
    {

        if(IS_AJAX)
        {
            $page = I('get.page',1,'int');
            $rows = I('get.rows',15,'int');
            $sord = I('get.sord','','string')?:'desc';
            $sidx = I('get.sidx','','string')?:'id';

            $sContent = I('get.sContent','','string');
            $daterange = I('get.daterange','','string');

            $operate_db = I('get.operate_db','','string');
            $operate_jb = I('get.operate_jb','','string');



            $wbid= session('wbid');
            if(empty($wbid))
            {
                $wbid =-1;
            }

            $map = array();
            if(!empty($wbid))
            {
                $map['wbid']= session('wbid');
            }


            if(!empty($sContent ))
            {
                $map['bz']= array('LIKE',"%$sContent%");
            }

            if(!empty($operate_db ))
            {
                $name=D('Yuangong')->where(array('WB_ID'=>session('wbid'),'id'=>$operate_db))->getField('name');
                $map['operate_db']= array('LIKE',"%$name%");
            }

            if(!empty($operate_jb ))
            {
                $name=D('Yuangong')->where(array('WB_ID'=>session('wbid'),'id'=>$operate_jb))->getField('name');
                $map['operate_jb']= array('LIKE',"%$name%");
            }


            if(!empty($daterange))
            {
                list($start,$end) = explode(' - ',$daterange);
                $start = str_replace('/','-',$start);
                $end = str_replace('/','-',$end);
                $map['dtEndTime']=array('BETWEEN',array($start,$end));
            }


            //   处理分页
            $count=D('Newproductjb')->getjbtongjilistByMap_count($map);
            $rows=15;
            $sql_page=ceil($count/$rows);

            if($page<=0)   $page=1;
            if($page>$sql_page)
            {
                $page=1;
            }else
            {

            }
			
			$from_last_je =D('Newproductjb')->where($map)->sum('from_last_je');
			//echo  D('Newproductjb')->getLastSql();
			//return;
			
			$to_nextshift_je=D('Newproductjb')->where($map)->sum('to_nextshift_je');
			

            $spjhdata = D('Newproductjb')->getjbtongjilistByMap($map,"$sidx $sord",$page,$rows);
            $count     = $spjhdata['count'];

            $response = new \stdClass();
			
			$response->to_nextshift_je     = $to_nextshift_je ;
			$response->from_last_je     = $from_last_je ;
			
            $response->count       = $spjhdata['count'];
            $response->nowPage     = $page ;
            $response->total       = ceil($spjhdata['count'] / $rows);

            $response->rows   = $spjhdata['list'] ;
            $this->ajaxReturn($response);
        }
    }
    
    
    
    public function xq_jiaobanmx()
    {
       
       $post_order_no=I('get.post_order_no','','string');
        session('post_order_no',$post_order_no);
        $wbid=session('wbid');
        $goodslist_str=D('Newproductjb')->where(array('wbid'=>$wbid,'post_order_no'=>$post_order_no))->getField('detailinfo');
  
        $goodslist=json_decode($goodslist_str,true);

        $allgoodslist=D('Newproduct')->field('goods_id,goods_name')->where(array('wbid'=>$wbid))->select();
      
        foreach ($goodslist as &$val) 
        {
            foreach($allgoodslist as &$val1)
            {
                if($val['goods_id']==$val1['goods_id'])
                {
                    $val['goods_name']=$val1['goods_name'];
                    break;
                }   
            }
        }


        $this->assign('goodslist',$goodslist);    
        $this->display();
        
    }



    
 
   


    //-------------------------交班明细---------------------------------

    //==========================商品统计========================================
    public function sptj()
    {
        $bOpen=$this->check_newcs_qx();
        if($bOpen===false)
        {
            $this->redirect('rate/fujia', array('cate_id' => 2), 1, '请在附加设置里开启新超市权限...');
        }
        $wbid= session('wbid');
        $typelist=D('ProductType')->select();
        $this->assign('typelist',$typelist);
        $yglist=D('Yuangong')->field('id,name')->where(array('WB_ID'=>$wbid))->select();
        $this->assign('yglist',$yglist);
        $this->display();
    }
    public function getxstongjilist_zongzhang()
    {
        if(IS_AJAX)
        {
            $page = I('get.page',1,'int');
            $rows = I('get.rows',10,'int');
            $sord = I('get.sord','','string')?:'desc';
            $sidx = I('get.sidx','','string')?:'dtInsertTime';


            $sContent = I('get.sContent','','string');
            $daterange = I('get.daterange','','string');
            $ordertype = I('get.ordertype','','string');
            $paytype = I('get.paytype','','string');
            $operate = I('get.operate','','string');
            $type_id = I('get.type_id','','string');

            $map = array();
            $map['wbid']=session('wbid');
            $map['is_zuhe']=array('neq',1); 
          
            if(!empty($daterange))
            {
                list($start,$end) = explode(' - ',$daterange);
                $start = str_replace('/','-',$start);
                $end = str_replace('/','-',$end);
                $map['dtInsertTime'] = array('BETWEEN',array($start,$end));
            }
 /*

            if(!empty($ordertype))
            {
                $map['ordertype']=$ordertype ;
            }
            if(!empty($type_id))
            {
                $map['type_id']=$type_id ;
            }

       


            if(!empty($operate ))
            {
                $name=D('Yuangong')->where(array('WB_ID'=>session('wbid'),'id'=>$operate))->getField('name');
                $map['operate']= array('LIKE',"%$name%");
            }

            if(!empty($paytype))
            {
                $map['paytype']=$paytype ;
            }


            if(!empty($sContent ))
            {
                $map['goods_name|post_order_no']= array('LIKE',"%$sContent%");
            }
            */


            $count= D('Newproduct')->getProductinfoListByMap_zongzhang_zuhe_02_count($map);
            $sql_page=ceil($count/$rows);
            if($page>$sql_page) $page=1;
            $wblist = D('Newproduct')->getProductinfoListByMap_zongzhang_zuhe_02($map,"$sidx $sord",$page,$rows);


        



  
            $response = new \stdClass();
            $response->records = $wblist['count'];
            $response->page = $page;
            $response->total = ceil($wblist['count'] / $rows);


            foreach($wblist['list'] as $key => $value)
            {
                $response->rows[$key]['id'] = $key;
                $response->rows[$key]['cell'] = $value;
            }
            $this->ajaxReturn($response);
        }
    }



    public function xiaoshou_mx_zongzhang()
    {
        $goods_id=I('get.goods_id','','string');
        $this->assign('goods_id',$goods_id);        
        $this->display();
    }
    
    public function getxstongjilist_mx_zongzhang()
    {
        if(IS_AJAX)
        {
            $page = I('get.page',1,'int');
            $rows = I('get.rows',20,'int');
            $sord = I('get.sord','','string')?:'desc';
            $sidx = I('get.sidx','','string')?:'dtInsertTime';

            $goods_id    = I('get.goods_id','','string'); 
            $post_order_no = I('get.cardno1','','string');
            $daterange     = I('get.daterange1','','string');
                                         
            $map = array();     
            $map['wbid']=session('wbid');
            $map['goods_id']=$goods_id; 
            
            if(!empty($post_order_no))
            {
              $map['post_order_no']=array('LIKE','%'.$post_order_no.'%');
            }  
                        
            if(!empty($daterange))  
            {
              list($start,$end) = explode(' - ',$daterange);    
              $start = str_replace('/','-',$start);            
              $end = str_replace('/','-',$end);                
              $map['dtInsertTime'] = array('BETWEEN',array($start,$end));
            }
                                            
            $count= D('Newproductxsmx')->getxstongji_mx_listByMap_count_zongzhang($map);
            $sql_page=ceil($count/$rows);  
            if($page>$sql_page) $page=1;    
            $wblist = D('Newproductxsmx')->getxstongji_mx_listByMap_zongzhang($map,"$sidx $sord",$page,$rows); 
            
            
            
            $response = new \stdClass();
            $response->records = $wblist['count'];
            $response->page = $page;
            $response->total = ceil($wblist['count'] / $rows);
            foreach($wblist['list'] as $key => $value)
            {       
              $response->rows[$key]['id'] = $key;
              $response->rows[$key]['cell'] = $value;
            }
            $this->ajaxReturn($response);
        }  
    }
    




    public function sptj_mx_zongzhang()
    {
        $goods_id=I('get.goods_id','','string');
        $this->assign('goods_id',$goods_id);        
        $this->display();
    }
    
    public function getsptj_mx_zongzhang()
    {
        if(IS_AJAX)
        {
            $page = I('get.page',1,'int');
            $rows = I('get.rows',20,'int');
            $sord = I('get.sord','','string')?:'desc';
            $sidx = I('get.sidx','','string')?:'dtInsertTime';

            $goods_id    = I('get.goods_id','','string'); 
            $post_order_no = I('get.cardno1','','string');
            $daterange     = I('get.daterange1','','string');
                                         
            $map = array();     
            $map['wbid']=session('wbid');
            $map['goods_id']=$goods_id; 
            $map['zh_or_cf']=0; 

            $is_zuhe=D('Newproduct')->where($map)->getField('is_zuhe');
            
            if(!empty($post_order_no))
            {
              $map['post_order_no']=array('LIKE','%'.$post_order_no.'%');
            }  
                        
            if(!empty($daterange))  
            {
              list($start,$end) = explode(' - ',$daterange);    
              $start = str_replace('/','-',$start);            
              $end = str_replace('/','-',$end);                
              $map['dtInsertTime'] = array('BETWEEN',array($start,$end));
            }
               

            if($is_zuhe==0)
            {
                $count= D('Newproductxsmx')->getxstongji_mx_listByMap_count($map);
                $sql_page=ceil($count/$rows);  
                if($page>$sql_page) $page=1;    
                $wblist = D('Newproductxsmx')->getxstongji_mx_listByMap($map,"$sidx $sord",$page,$rows); 
            }
            else if($is_zuhe==1)
            {
                $count= D('Newproductsxjmx')->getsxj_mx_ListByMap_count($map);
                $sql_page=ceil($count/$rows);  
                if($page>$sql_page) $page=1;    
                $wblist = D('Newproductsxjmx')->getsxj_mx_ListByMap($map,"$sidx $sord",$page,$rows); 
            }    


                 
            $response = new \stdClass();
            $response->records = $wblist['count'];
            $response->page = $page;
            $response->total = ceil($wblist['count'] / $rows);
            foreach($wblist['list'] as $key => $value)
            {       
              $response->rows[$key]['id'] = $key;
              $response->rows[$key]['cell'] = $value;
            }
            $this->ajaxReturn($response);
        }  
    }




     public function zh_sptj_mx_zongzhang()
    {
        $goods_id=I('get.goods_id','','string');
        $this->assign('goods_id',$goods_id);        
        $this->display();
    }
    
    public function getzhsptj_mx_zongzhang()
    {
        if(IS_AJAX)
        {
            $page = I('get.page',1,'int');
            $rows = I('get.rows',20,'int');
            $sord = I('get.sord','','string')?:'desc';
            $sidx = I('get.sidx','','string')?:'dtInsertTime';

            $goods_id    = I('get.goods_id','','string'); 
            $post_order_no = I('get.cardno1','','string');
            $daterange     = I('get.daterange1','','string');
                                         
            $map = array();     
            $map['wbid']=session('wbid');
            $map['goods_id']=$goods_id; 
            $map['zh_or_cf']=1; 

            $is_zuhe=D('Newproduct')->where($map)->getField('is_zuhe');
            
            if(!empty($post_order_no))
            {
              $map['post_order_no']=array('LIKE','%'.$post_order_no.'%');
            }  
                        
            if(!empty($daterange))  
            {
              list($start,$end) = explode(' - ',$daterange);    
              $start = str_replace('/','-',$start);            
              $end = str_replace('/','-',$end);                
              $map['dtInsertTime'] = array('BETWEEN',array($start,$end));
            }
               

             if($is_zuhe==1)
            {
                $count= D('Newproductsxjmx')->getsxj_mx_ListByMap_count($map);
                $sql_page=ceil($count/$rows);  
                if($page>$sql_page) $page=1;    
                $wblist = D('Newproductsxjmx')->getsxj_mx_ListByMap($map,"$sidx $sord",$page,$rows); 
            }    


                 
            $response = new \stdClass();
            $response->records = $wblist['count'];
            $response->page = $page;
            $response->total = ceil($wblist['count'] / $rows);
            foreach($wblist['list'] as $key => $value)
            {       
              $response->rows[$key]['id'] = $key;
              $response->rows[$key]['cell'] = $value;
            }
            $this->ajaxReturn($response);
        }  
    }




    public function getShijiproductxslist()
    {
        if(IS_AJAX)
        {
            $page = I('get.page',1,'int');
            $rows = I('get.rows',30,'int');
            $sord = I('get.sord','','string')?:'desc';
            $sidx = I('get.sidx','','string')?:'dtInsertTime';


            $sContent = I('get.sContent','','string');
            $daterange = I('get.daterange6','','string');
           // $ordertype = I('get.ordertype','','string');
           // $paytype = I('get.paytype','','string');
          //  $operate = I('get.operate','','string');
          //  $type_id = I('get.type_id','','string');

            $map = array();
            $map['wbid']=session('wbid');
            $map['is_zuhe']=array('neq',2); 
          
		    $map1=array(); 
            if(!empty($daterange))
            {
                list($start,$end) = explode(' - ',$daterange);
                $start = str_replace('/','-',$start);
                $end = str_replace('/','-',$end);
                $map1['dtInsertTime'] = array('BETWEEN',array($start,$end));
            }

			$map1['wbid']=session('wbid');
            $map1['is_zuhe']=array('neq',2);

            $count= D('Newproduct')->getProductinfoListByMap_zongzhang_zuhe_01_count($map);
            $sql_page=ceil($count/$rows);
            if($page>$sql_page) $page=1;
            $wblist = D('Newproduct')->getProductinfoListByMap_zongzhang_zuhe_01($map,"$sidx $sord",$page,$rows,$map1);


        
            $response = new \stdClass();
            $response->records = $wblist['count'];
            $response->page = $page;
            $response->total = ceil($wblist['count'] / $rows);


            foreach($wblist['list'] as $key => $value)
            {
                $response->rows[$key]['id'] = $key;
                $response->rows[$key]['cell'] = $value;
            }
            $this->ajaxReturn($response);
        }
    }


    //=====================================进出货页面开始=================================================
    public function jinhuo()
    {
        session('goods_id_list',null);
        session('plch_status','3');
        $wbid=session('wbid');

        $map['wbid']=$wbid;
        $map['deleted'] =0;
        $map['is_zuhe'] =array('neq',2);
        $goodslist=D('Newproduct')->where($map)->select();
        foreach($goodslist as &$val)
        {
            $val['value']=$val['goods_pinyin'].','.$val['goods_name'].','.$val['goods_quanpin'];
            $val['label']=$val['goods_pinyin'].','.$val['goods_name'].','.$val['goods_quanpin'];
            $val['hj_num']=$val['kc_num'];
            $val['one_jian_jin_price']=0;
            $val['one_ge_jin_price']=0;


        }
        $this->assign('goodslist',json_encode($goodslist));

        creatToken();
        $this->display();
    }
    //===============================进货页面====================
    public function jinhuo_edit_set()
    {
        if(IS_AJAX)
        {
            if(!checkToken($_POST['token']))
            {
                writelog('jinhuo_edit_set---重复提交');
                $data['status']=-2;
                $this->ajaxReturn($data);
                return;
            }


            $wbid=session('wbid');
            $post_order_no = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);  //网站订单号
            $post_order_no='JH'.$post_order_no;
            $dtInsertTime=date('Y-m-d H:i:s',time());
            $str=I('post.goodsinfo','','string');

            $unit=I('post.unit','','string');  //按件按个
            $sumje=I('post.sumje','','string');

            $str=htmlspecialchars_decode($str);
            $jinhuo_goodslist=json_decode($str,true);
            if(empty($jinhuo_goodslist))
            {
                $data['status']=-1;
                $this->ajaxReturn($data);
                return;
            }



            $all_googs_list=array();
            $all_googs_list=D('Newproduct')->field('goods_id,goods_name,ck_num,kc_num')->where(array('wbid'=>$wbid))->select();

            $info=' 总金额：'.$sumje.',';
            $result=true;
            D()->startTrans();  //启用事务

            foreach( $jinhuo_goodslist as $val)
            {

                $jinhuomx_insert_data['goods_id']=$val['goods_id'];
                $jinhuomx_insert_data['changenum']  =$val['sumnum'];

                foreach($all_googs_list as $val2)
                {
                    if($val2['goods_id']==$val['goods_id'])
                    {
                        $jinhuomx_insert_data['old_hj_num']  =$val2['kc_num'];
                        $jinhuomx_insert_data['old_ck_num']  =$val2['ck_num'];
                        $goods_name=$val2['goods_name'] ;
                        break;
                    }
                }

                $jinhuomx_insert_data['price']=$val['price'];
                $jinhuomx_insert_data['sumje']=$val['price']*$val['sumnum'];
                $jinhuomx_insert_data['post_order_no']=$post_order_no;
                $jinhuomx_insert_data['jch_type']=1;
                $jinhuomx_insert_data['wbid']=$wbid;
                $jinhuomx_insert_data['dtInsertTime']=$dtInsertTime;
                $jinhuomx_insert_data['operate']=session('username');


                if(D('Newproductjchmx')->add($jinhuomx_insert_data)===false)
                {
                    $result=false;

                }


                //直接存到该商品的仓库里
                if((D('NewProduct')->where(array('wbid'=>$wbid,'goods_id'=>$val['goods_id']))->setInc('ck_num',$val['sumnum']))===false)
                {
                    $result=false;

                }
                $info.= $goods_name.':'.$val['sumnum'].'个'.' ';
            }

            //=========================添加所有的组合商品的明细数据=================================

            //更新库存表
            $jinhuo_insert_data['post_order_no']=$post_order_no;
            $jinhuo_insert_data['jch_type']=1;
            $jinhuo_insert_data['wbid']=$wbid;
            $jinhuo_insert_data['info']=$info;
            $jinhuo_insert_data['dtInsertTime']=$dtInsertTime;
            $jinhuo_insert_data['sumje']=$sumje;
            $jinhuo_insert_data['bz']=I('post.bz','','string');
            $jinhuo_insert_data['operate']=session('username');

            if(D('Newproductjch')->add($jinhuo_insert_data)===false)
            {
                writelog('----11------');
                $result=false;
            }

            if($result)
            {
                writelog('----12------');
                D()->commit();  //提交事务
                $data['status']=1;
            }
            else
            {
                D()->rollback();    //回滚
                $data['status']=-1;
            }


            $this->ajaxReturn($data);
        }
    }
    public function getcktjmx()
    {
        if(IS_AJAX)
        {
            $page = I('get.page',1,'int');
            $rows = I('get.rows',15,'int');
            $sord = I('get.sord','','string')?:'desc';
            $sidx = I('get.sidx','','string')?:'id';

            $daterange = I('get.daterange','','string');//获取交班时间
            $jch_type =    I('get.jch_type','0','string');
            $sContent =    I('get.sContent','','string');
            $operate =     I('get.operate','','string');


            $wbid= session('wbid');
            if(empty($wbid))
            {
                $wbid =-1;
            }
            $map = array();
            if(!empty($wbid))
            {
                $map['wbid']= $wbid;
            }


            if(!empty($jch_type ))
            {

                $map['jch_type']= $jch_type;
            }

            if(!empty($operate ))
            {
                $name=D('Yuangong')->where(array('WB_ID'=>session('wbid'),'id'=>$operate))->getField('name');
                $map['operate']= array('LIKE',"%$name%");
            }

            if(!empty($sContent))
            {
                $map['goods_name']=array('LIKE',"%$sContent%");
            }


            if(!empty($daterange))
            {
                list($start,$end) = explode(' - ',$daterange);
                $start = str_replace('/','-',$start);
                $end = str_replace('/','-',$end);
                $map['dtInsertTime']=array('BETWEEN',array($start,$end));
            }

            $count=D('Newproductjch')->gejhtongjilistByMap_count($map);

            $rows=15;
            $sql_page=ceil($count/$rows);

            if($page<=0)   $page=1;
            if($page>$sql_page)
            {
                $page=1;
            }
            else
            {

            }

            $jhtjdata = D('Newproductjch')->gejhtongjilistByMap($map,"$sidx $sord",$page,$rows);

            $count    = $jhtjdata['count'];

            $response = new \stdClass();
            $response->count       = $jhtjdata['count'];//返回的数组的第一个字段记录总条数
            $response->nowPage     = $page ;              //每页显示的记录数目
            $response->total       = ceil($jhtjdata['count'] / $rows);

            $response->rows          = $jhtjdata['list'] ;
            $response->tongji_list   = $jhtjdata['tongji_list'] ;
            $response->tongji_count   = $jhtjdata['tongji_count'] ;
            $this->ajaxReturn($response);
        }

    }
    public function chuhuo()
    {
        session('goods_id_list',null);
        session('plch_status','4');

        $wbid=session('wbid');
        if(empty($wbid))
        {
            echo  'error';
            return;
        }
        $map=array();
        $map['deleted']=0;
        $map['wbid']=$wbid;
        $map['ck_num']=array('gt',0);

        $goodslist=D('Newproduct')->field('goods_id,goods_name,ck_num,kc_num')->where($map)->select();
        foreach($goodslist as &$val)
        {
            $val['value']=$val['goods_pinyin'].','.$val['goods_name'].','.$val['goods_quanpin'];
            $val['label']=$val['goods_pinyin'].','.$val['goods_name'].','.$val['goods_quanpin'];
            $val['hj_num']=$val['kc_num'];
            $val['one_jian_jin_price']=0;
            $val['one_ge_jin_price']=0;


        }
        $this->assign('goodslist',json_encode($goodslist));
        creatToken();
        $this->display();
    }
    public function chuhuo_edit_set()
    {
        if(IS_AJAX)
        {
            if(!checkToken($_POST['token']))
            {
                writelog('jinhuo_edit_set---重复提交');
                $data['status']=-2;
                $this->ajaxReturn($data);
                return;
            }
            else
            {
                writelog('jinhuo_edit_set---未重复提交');
            }
            $wbid=session('wbid');
            $post_order_no = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);  //网站订单号
            $post_order_no='CH'.$post_order_no;
            $dtInsertTime=date('Y-m-d H:i:s',time());
            $str=I('post.goodsinfo','','string');

            $unit=I('post.unit','','string');  //按件按个
            $sumje=I('post.sumje','','string');

            $str=htmlspecialchars_decode($str);
            $jinhuo_goodslist=json_decode($str,true);
            if(empty($jinhuo_goodslist))
            {
                $data['status']=-1;
                $this->ajaxReturn($data);
                return;
            }


            $all_googs_list=array();
            $all_googs_list=D('Newproduct')->field('goods_id,goods_name,ck_num,kc_num')->where(array('wbid'=>$wbid))->select();

            $info=' 总金额：'.$sumje.',';
            $result=true;
            D()->startTrans();  //启用事务

            foreach( $jinhuo_goodslist as $val)
            {
                $jinhuomx_insert_data['goods_id']=$val['goods_id'];
                $jinhuomx_insert_data['changenum']  =$val['sumnum'];

                foreach($all_googs_list as $val2)
                {
                    if($val2['goods_id']==$val['goods_id'])
                    {
                        $jinhuomx_insert_data['old_hj_num']  =$val2['kc_num'];
                        $jinhuomx_insert_data['old_ck_num']  =$val2['ck_num'];
                        $goods_name=$val2['goods_name'] ;
                        break;
                    }
                }

                $jinhuomx_insert_data['price']=$val['price'];
                $jinhuomx_insert_data['sumje']=$val['price']*$val['sumnum'];
                $jinhuomx_insert_data['post_order_no']=$post_order_no;
                $jinhuomx_insert_data['jch_type']=2;
                $jinhuomx_insert_data['wbid']=$wbid;
                $jinhuomx_insert_data['dtInsertTime']=$dtInsertTime;
                $jinhuomx_insert_data['operate']=session('username');

                if(D('Newproductjchmx')->add($jinhuomx_insert_data)===false)
                {
                    $result=false;
                }

                //直接存到该商品的仓库里
                if((D('NewProduct')->where(array('wbid'=>$wbid,'goods_id'=>$val['goods_id']))->setDec('ck_num',$val['sumnum']))===false)
                {
                    $result=false;
                }
                $info.= $goods_name.':'.$val['sumnum'].'个'.' ';
            }

            //=========================添加所有的组合商品的明细数据=================================

            //更新库存表
            $jinhuo_insert_data['post_order_no']=$post_order_no;
            $jinhuo_insert_data['jch_type']=2;
            $jinhuo_insert_data['wbid']=$wbid;
            $jinhuo_insert_data['info']=$info;
            $jinhuo_insert_data['dtInsertTime']=$dtInsertTime;
            $jinhuo_insert_data['sumje']=$sumje;
            $jinhuo_insert_data['bz']=I('post.bz','','string');
            $jinhuo_insert_data['operate']=session('username');

            if(D('Newproductjch')->add($jinhuo_insert_data)===false)
            {
                writelog('----11------');
                $result=false;
            }

            if($result)
            {
                writelog('----12------');
                D()->commit();  //提交事务
                $data['status']=1;
            }
            else
            {
                D()->rollback();    //回滚
                $data['status']=-1;
            }

            $this->ajaxReturn($data);
        }
    }

    public function plch_jch()
    {
        $goods_id_list=I('get.goods_id','0','string');
        if($goods_id_list=='null')
        {

        }else
        {
            session('goods_id_list',$goods_id_list);
        }
        $type_list=D('ProductType')->select();
        $this->assign('type_list',$type_list);
        $this->display();
    }
    public function getshangpininfo_plch_list()
    {
        if(IS_AJAX)
        {
            $page = I('get.page',1,'int');
            $rows = I('get.rows',20,'int');
            $sord = I('get.sord','','string')?:'asc';
            $sidx = I('get.sidx','','string')?:'goods_id';


            $goods_name = I('get.goods_name','','string');
            $goods_type = I('get.goods_type','','string');


            $goods_id_list=session('goods_id_list');
            $plch_status=session('plch_status');

            $map = array();
            $map['deleted']=0;
            $map['wbid']=session('wbid');

            if($plch_status=='3')  //进货
            {
                $map['is_zuhe']=array('neq',2);

            }
            else  if($plch_status=='4')  //出货
            {
                 $map['is_zuhe']=array('neq',2);
                 $map['ck_num']=array('gt',0);
            }

            if(($goods_id_list !='null') &&(!empty($goods_id_list)) && ($goods_id_list!='undefined'))
            {
                $map['goods_id']=array('not in',$goods_id_list);
            }

            if(!empty($goods_name ))
            {
                $map['goods_name']=array('LIKE','%'.$goods_name.'%');
            }

            $count= D('Newproduct')->getProductinfoListByMap_count($map);
            $sql_page=ceil($count/$rows);
            if($page>$sql_page) $page=1;
            $wblist = D('Newproduct')->getProductinfoListByMap($map,"$sidx $sord",$page,$rows);




            $response = new \stdClass();
            $response->records = $wblist['count'];
            $response->page = $page;
            $response->total = ceil($wblist['count'] / $rows);
            foreach($wblist['list'] as $key => $value)
            {
                $response->rows[$key]['id'] = $key;
                $response->rows[$key]['cell'] = $value;
            }
            $this->ajaxReturn($response);
        }
    }


    /*==================================上下架beg==================================*/
    public function shangjia(){
        $bOpen=$this->check_newcs_qx();
        if($bOpen===false)
        {
            //$this->redirect('rate/fujia', array('cate_id' => 2), 1, '请在附加设置里开启设置...');
        }
        session('goods_id_list',null);
        $wbid=session('wbid');
        session('plch_status',1);  //上架

        //单个有仓库库存商品的列表
        $map=array();
        $map['wbid']=$wbid;
        $map['deleted']=0;
        $map['ck_num']=array('gt',0);
        $map['is_zuhe']=array('neq',2);
        $goodslist=D('Newproduct')->where($map)->select();
        foreach($goodslist as  &$val) {
            $val['hj_num']=$val['kc_num'];
        }

       // print_r(D('Newproduct')->getLastSql());
      //  return;
        $this->assign('goodslist',json_encode($goodslist));

        $map=array();
        $map['is_zuhe']=2;
        $map['deleted']=0;
        $map['wbid']=$wbid;

        $zuhegoods_list=D('Newproduct')->getProductinfoListByMap2($map);
        foreach($zuhegoods_list as  &$val) {
            $val['hj_num']=$val['kc_num'];
        }

        $this->assign('zuhegoods_list',json_encode($zuhegoods_list));
        // is_zuhe=0  is_zuhe=2 的所有商品
        $map=array();
        $map['is_zuhe']=array('neq',1);
        $map['deleted']=0;
        $map['wbid']=$wbid;
        $all_goodsid_list=D('Newproduct')->Field('is_zuhe,goods_id')->where($map)->select();

        $this->assign('all_goodsid_list',json_encode($all_goodsid_list));

        creatToken();
        $this->display();
    }
    public function xiajia()
    {

        session('goods_id_list',null);
        $wbid=session('wbid');
        session('plch_status',2);  //下架

        //单个有仓库库存商品的列表
        $map=array();
        $map['wbid']=$wbid;
        $map['deleted']=0;
        $map['kc_num']=array('gt',0);
        $map['is_zuhe']=array('neq',1);
        $goodslist=D('Newproduct')->where($map)->select();
        foreach($goodslist as  &$val) {
            $val['hj_num']=$val['kc_num'];
        }

       // print_r(D('Newproduct')->getLastSql());
      //  return;
        $this->assign('goodslist',json_encode($goodslist));

        $map=array();
        $map['is_zuhe']=2;
        $map['deleted']=0;  
        $map['wbid']=$wbid;

        $zuhegoods_list=D('Newproduct')->getProductinfoListByMap2($map);
        foreach($zuhegoods_list as  &$val) {
            $val['hj_num']=$val['kc_num'];
        }

        $this->assign('zuhegoods_list',json_encode($zuhegoods_list));
        // is_zuhe=0  is_zuhe=2 的所有商品
        $map=array();
        $map['is_zuhe']=array('neq',1);
        $map['deleted']=0;
        $map['wbid']=$wbid;
        $all_goodsid_list=D('Newproduct')->Field('is_zuhe,goods_id')->where($map)->select();
        
        $this->assign('all_goodsid_list',json_encode($all_goodsid_list));


        creatToken();
        $this->display();
    }
    public function shangjia_edit_set()
    {
        $wbid=session('wbid');
        if(IS_AJAX)
        {
            if(!checkToken($_POST['token']))
            {
                writelog('shangjia_edit_set---重复提交');
                $data['status']=-2;
                $this->ajaxReturn($data);
                return;
            }


            $post_order_no = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);  //网站订单号
            $post_order_no='SJ'.$post_order_no;
            $dtInsertTime=date('Y-m-d H:i:s',time());
            $str=I('post.goodsinfo','','string');
            $str=htmlspecialchars_decode($str);
            $shangjia_goodslist=json_decode($str,true);
            if(empty($shangjia_goodslist))
            {
                $data['status']=-1;
                $this->ajaxReturn($data);
                return;
            }




            $info='';
            $result=true;
            D()->startTrans();  //启用事务
            foreach( $shangjia_goodslist as &$val)
            {
                $onegoodsinfo=D('Newproduct')->where(array('wbid'=>$wbid,'goods_id'=>$val['goods_id']))->find();
                $shangxiajiamx_insert_data=array();
                $shangxiajiamx_insert_data['goods_id']=$val['goods_id'];
                $shangxiajiamx_insert_data['num']     =$val['num'];
                $shangxiajiamx_insert_data['ck_num']  =$onegoodsinfo['ck_num'];
                $shangxiajiamx_insert_data['hj_num']  =$onegoodsinfo['kc_num'];
                $shangxiajiamx_insert_data['post_order_no']=$post_order_no;
                $shangxiajiamx_insert_data['shangxia_status']=0;
                $shangxiajiamx_insert_data['wbid']=$wbid;
                $shangxiajiamx_insert_data['operate']=session('username');
                $shangxiajiamx_insert_data['dtInsertTime']=$dtInsertTime;
                $shangxiajiamx_insert_data['ordertype']=1;
                



                if($val['num'] >= $onegoodsinfo['ck_num'])
                {
                    $now_sj_shangjia_num =$onegoodsinfo['ck_num'];
                }
                else
                {
                    $now_sj_shangjia_num =$val['num'];
                }

                if($val['is_zuhe']==0) //该商品的仓库库存减少，货架库存增加 position=1 货架   position=0 仓库
                {
                    $shangxiajiamx_insert_data['zuhe_id']=0;
                    $shangxiajiamx_insert_data['is_zuhe_goods']=0;
                    $shangxiajiamx_insert_data['zuhe_flag']=1;

                    if(D('Newproductsxjmx')->add($shangxiajiamx_insert_data)===false)
                    {
                        $result=false;
                    }
                    if(D('Newproduct')->where(array('wbid'=>$wbid,'goods_id'=>$val['goods_id']))->setDec('ck_num',$now_sj_shangjia_num)===false)
                    {
                        $result=false;
                    }

                    if(D('Newproduct')->where(array('wbid'=>$wbid,'goods_id'=>$val['goods_id']))->setInc('kc_num',$now_sj_shangjia_num)===false)
                    {
                        $result=false;
                    }

                }
                else if($val['is_zuhe']==1)
                {
                    if($now_sj_shangjia_num > 0)
                    {
                        $shangxiajiamx_insert_data['zuhe_id']=$onegoodsinfo['zuhe_id'];
                        $shangxiajiamx_insert_data['is_zuhe_goods']=1;
                        $shangxiajiamx_insert_data['zuhe_flag']=1;
                        $shangxiajiamx_insert_data['zh_or_cf']=1;
                   

                        
                        if(D('Newproductsxjmx')->add($shangxiajiamx_insert_data)===false)
                        {
                            $result=false;
                        }
                        if(D('Newproduct')->where(array('wbid'=>$wbid,'goods_id'=>$val['goods_id']))->setDec('ck_num',$now_sj_shangjia_num)===false)
                        {
                            $result=false;
                        }

                    }
                    else
                    {
                        continue;
                    }
                }
                else if($val['is_zuhe']==2)
                {
                    $shangxiajiamx_insert_data['zuhe_id']=0;
                    $shangxiajiamx_insert_data['is_zuhe_goods']=2;
                    $shangxiajiamx_insert_data['zuhe_flag']=1;
                    $shangxiajiamx_insert_data['zh_or_cf']=1;

                    if(D('Newproductsxjmx')->add($shangxiajiamx_insert_data)===false)
                    {
                        $result=false;
                    }
                    if(D('Newproduct')->where(array('wbid'=>$wbid,'goods_id'=>$val['goods_id']))->setInc('kc_num',$val['num'])===false)
                    {
                        $result=false;
                    }
                }

                $val['goods_name']=D('Product')->where(array('wbid'=>$wbid,'goods_id'=>$val['goods_id']))->getField('goods_name');
                $info.= $val['goods_name'].':'.$val['num'].' ';
            }

            //更新库存表

            $shangxiajia_insert_data=array();
            $shangxiajia_insert_data['post_order_no']=$post_order_no;
            $shangxiajia_insert_data['shangxia_status']=0;
            $shangxiajia_insert_data['wbid']=$wbid;
            $shangxiajia_insert_data['info']=$info;
            $shangxiajia_insert_data['dtInsertTime']=$dtInsertTime;
            $shangxiajia_insert_data['operator']=session('username');
            $shangxiajia_insert_data['bz']=I('post.bz','','string');
            $shangxiajia_insert_data['detailinfo']=$str;
            $shangxiajia_insert_data['zuhe_flag']=1;

            if(D('Newproductsxj')->add($shangxiajia_insert_data)===false)
            {
                $result=false;
            }


            if($result)
            {
                D()->commit();  //提交事务
                $data['status']=1;
            }
            else
            {
                D()->rollback();    //回滚
                $data['status']=-1;
            }

            $this->ajaxReturn($data);
        }
    }
    public function xiajia_edit_set()
    {
        $wbid= session('wbid');
        if(IS_AJAX)
        {
            if(!checkToken($_POST['token']))
            {
                writelog('xiajia_edit_set---重复提交');
                $data['status']=-2;
                $this->ajaxReturn($data);
                return;
            }
            else
            {
                //writelog('xiajia_edit_set---未重复提交');
            }

            $post_order_no = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);  //网站订单号
            $post_order_no='XJ'.$post_order_no;
            $dtInsertTime=date('Y-m-d H:i:s',time());
            $str=I('post.goodsinfo','','string');
            $str=htmlspecialchars_decode($str);
            $xiajia_goodslist=json_decode($str,true);

            if(empty($xiajia_goodslist))
            {
                $data['status']=1;
                $this->ajaxReturn($data);
                return;
            }


            $info='';
            $result=true;
            D()->startTrans();  //启用事务
            //is_zuhe=0 直接货架减少，库存增加


            foreach($xiajia_goodslist as &$val)
            {
                $map=array();
                $map['goods_id']=$val['goods_id'];
                $map['wbid']=$wbid;
                $goods_info=D('Newproduct')->where($map)->find();
                $shangxiajiamx_insert_data=array();
                $shangxiajiamx_insert_data['goods_id']=$val['goods_id'];
                $shangxiajiamx_insert_data['num']     =$val['num'];

                $shangxiajiamx_insert_data['post_order_no']=$post_order_no;
                $shangxiajiamx_insert_data['shangxia_status']=1;             //1 是下架
                $shangxiajiamx_insert_data['wbid']=$wbid;
                $shangxiajiamx_insert_data['dtInsertTime']=$dtInsertTime;
                $shangxiajiamx_insert_data['operate']=session('username');
                $shangxiajiamx_insert_data['ordertype']=2;


                //该商品的仓库库存减少，货架库存增加 position=1 货架   position=0 仓库
                
                if($val['is_zuhe']==0)
                {

                    //该商品的实际货架数量
                    $now_hjkc_num= $goods_info['kc_num'];
                    if($val['num'] >= $now_hjkc_num)
                    {
                        $now_sj_xiajia_num =$now_hjkc_num;
                    }
                    else
                    {
                        $now_sj_xiajia_num =$val['num'];
                    }
                    if(empty($now_sj_xiajia_num))
                    {
                       $now_sj_xiajia_num=0;
                    }

                    $shangxiajiamx_insert_data['ck_num']  =$goods_info['ck_num'];
                    $shangxiajiamx_insert_data['hj_num']  =$goods_info['kc_num'];
                    $shangxiajiamx_insert_data['zuhe_flag']=1;
                    $shangxiajiamx_insert_data['is_zuhe_goods']=0;
                    $shangxiajiamx_insert_data['shangxia_status']=1; 
                    $shangxiajiamx_insert_data['zuhe_id']=0;
                    if(D('Newproductsxjmx')->add($shangxiajiamx_insert_data)===false)
                    {
                        $result=false;
                    }

                    //仓库数量增加
                    
                    if(D('Newproduct')->where(array('wbid'=>$wbid,'goods_id'=>$val['goods_id']))->setInc('ck_num',$now_sj_xiajia_num)===false)
                    {
                        $result=false;
                    }
                    
                    //货架数量减少
                    
                    if(D('Newproduct')->where(array('wbid'=>$wbid,'goods_id'=>$val['goods_id']))->setDec('kc_num',$now_sj_xiajia_num)===false)
                    {
                        $result=false;
                    }
                    
                }
                
                else if($val['is_zuhe']==1)
                {
                    //该商品的仓库数量增加
                    if($val['num'] > 0)
                    {
                        $shangxiajiamx_insert_data['ck_num']  =$goods_info['ck_num'];
                        $shangxiajiamx_insert_data['hj_num']  =0;
                        $shangxiajiamx_insert_data['zuhe_flag']=1;
                        $shangxiajiamx_insert_data['is_zuhe_goods']=1;
                        $shangxiajiamx_insert_data['zuhe_id']=$goods_info['zuhe_id'];
                        $shangxiajiamx_insert_data['shangxia_status']=1; 
                        $shangxiajiamx_insert_data['zh_or_cf']=1;

                        if(D('Newproductsxjmx')->add($shangxiajiamx_insert_data)===false)
                        {
                            $result=false;
                        }

                        if(D('Newproduct')->where(array('wbid'=>$wbid,'goods_id'=>$val['goods_id']))->setInc('ck_num',$val['num'])===false)
                        {
                            $result=false;
                        }
                    }
                    else
                    {
                        continue;
                    }

                }
                else if($val['is_zuhe']==2)
                {

                    //该商品的实际货架数量
                    $now_hjkc_num= $goods_info['kc_num'];
                    if($val['num'] >= $now_hjkc_num)
                    {
                        $now_sj_xiajia_num =$now_hjkc_num;
                    }
                    else
                    {
                        $now_sj_xiajia_num =$val['num'];
                    }
                    if(empty($now_sj_xiajia_num))
                    {
                       $now_sj_xiajia_num=0;
                    }

                    //该组合商品的货架数量减少
                    if(D('Newproduct')->where(array('wbid'=>$wbid,'goods_id'=>$val['goods_id']))->setDec('kc_num',$now_sj_xiajia_num)===false)
                    {
                        $result=false;
                    }

                    $shangxiajiamx_insert_data['ck_num']  =0;
                    $shangxiajiamx_insert_data['hj_num']  =$goods_info['kc_num'];
                    $shangxiajiamx_insert_data['zuhe_flag']=1;
                    $shangxiajiamx_insert_data['is_zuhe_goods']=2;
                    $shangxiajiamx_insert_data['zuhe_id']=0;
                    $shangxiajiamx_insert_data['shangxia_status']=1; 
                    $shangxiajiamx_insert_data['zh_or_cf']=1;
                    if(D('Newproductsxjmx')->add($shangxiajiamx_insert_data)===false)
                    {
                        $result=false;
                    }
                }


                $val['goods_name']=D('Newproduct')->where(array('wbid'=>$wbid,'goods_id'=>$val['goods_id']))->getField('goods_name');
                $info.= $val['goods_name'].':'.$val['num'].' ';
                
            }

            //更新库存表
            
            $shangxiajia_insert_data['post_order_no']=$post_order_no;
            $shangxiajia_insert_data['shangxia_status']=1;
            $shangxiajia_insert_data['wbid']=$wbid;
            $shangxiajia_insert_data['info']=$info;
            $shangxiajia_insert_data['dtInsertTime']=$dtInsertTime;
            $shangxiajia_insert_data['bz']=I('post.bz','','string');
            $shangxiajia_insert_data['operator']=session('username');
            $shangxiajia_insert_data['detailinfo']=$str;
            $shangxiajia_insert_data['zuhe_flag']=1;

            if(D('Newproductsxj')->add($shangxiajia_insert_data)===false)
            {
                $result=false;
            }
            

            if($result)
            {
                D()->commit();  //提交事务

                $data['status']=1;
            }
            else
            {
                D()->rollback();    //回滚
                $data['status']=-1;
            }

            $this->ajaxReturn($data);
        }
    }

    public function plch_sxj()
    {
        $goods_id_list=I('get.goods_id','0','string');
        if($goods_id_list=='null')
        {

        }
        else
        {
            session('goods_id_list',$goods_id_list);
        }
        $type_list=D('ProductType')->select();
        $this->assign('type_list',$type_list);
        $this->display();
    }
    public function getshangpininfo_plch_sxj_list()
    {
        if(IS_AJAX)
        {
            $page = I('get.page',1,'int');
            $rows = I('get.rows',20,'int');
            $sord = I('get.sord','','string')?:'asc';
            $sidx = I('get.sidx','','string')?:'goods_id';
            $plch_status=session('plch_status'); 

           

            $goods_name = I('get.goods_name','','string');
            $goods_type = I('get.goods_type','','string');

            $map = array();

            $map['sp.deleted']=0;
            if($plch_status==1)  //上架
            {
               $map['sp.ck_num']=array('gt',0);
               $map['sp.is_zuhe']=array('neq',2);
            }
            else if($plch_status==2)//下架
            {
               $map['sp.kc_num']=array('gt',0);
               $map['sp.is_zuhe']=array('neq',1);
            }    


            
            $map['sp.wbid']=session('wbid');
            $goods_id_list=session('goods_id_list');



            if(($goods_id_list !='null') &&(!empty($goods_id_list)) && ($goods_id_list!='undefined'))
            {
                $map['sp.goods_id']=array('not in',$goods_id_list);
            }


            $count= D('Newproduct')->getProductinfoListByMap_count($map);
            $sql_page=ceil($count/$rows);
            if($page>$sql_page) $page=1;
            $wblist = D('Newproduct')->getProductinfoListByMap($map,"$sidx $sord",$page,$rows);


            $response = new \stdClass();
            $response->records = $wblist['count'];
            $response->page = $page;
            $response->total = ceil($wblist['count'] / $rows);
            foreach($wblist['list'] as $key => $value)
            {
                $response->rows[$key]['id'] = $key;
                $response->rows[$key]['cell'] = $value;
            }
            $this->ajaxReturn($response);
        }
    }
    /*==================================上下架 end==================================*/

    //==========================================商品信息开始==================================
    public function shangpin(){
        $bOpen=$this->check_newcs_qx();
        if($bOpen===false)
        {
            //$this->redirect('rate/fujia', array('cate_id' => 2), 1, '请在附加设置里开启设置...');
        }
        $wbid=session('wbid');
        $typelist=D('ProductType')->select();
        $this->assign('typelist',$typelist);
        $map = array();
        $map['wbid']=session('wbid');
        $map['is_zuhe']=array('neq',1);
        $map['deleted']=0;
        $sumnum=D('Newproduct')->where($map)->count();
        $this->assign('sumnum',$sumnum);
        $this->display();
    }
    public function shangpin_add(){
        $type_list=D('ProductType')->select();
        $this->assign('type_list',$type_list);
        session('goods_id_list',null);
        $wbid=session('wbid');
        $goodslist=D('Productinfomb')->select();
        foreach($goodslist as &$val)
        {
            $val['value']=$val['goods_pinyin'].','.$val['goods_name'].','.$val['goods_quanpin'];
            $val['label']=$val['goods_pinyin'].','.$val['goods_name'].','.$val['goods_quanpin'];
            $val['goods_image_moren']=$val['goods_image'];
            $val['goods_image']=C('SHANGPIN_MUBAN_TUPIAN_PATH_URL').$val['goods_image'];
        }
        $this->assign('goodslist',json_encode($goodslist));
        $this->display();
    }
    public function shangpin_add_set(){
        $wbid=session('wbid');
        if(IS_AJAX)
        {
            $result=true;
            $data['status']=-1;
            D()->startTrans();
            $dtInsertTime= date('Y-m-d H:i:s',time());
            $one_goods_name=I('post.goods_name','','string');
            if(D('Newproduct')->where(array('wbid'=>session('wbid'),'goods_name'=>$one_goods_name,'deleted'=>0))->find())
            {
                $data['status']=-2;
                $this->ajaxReturn($data);
                return;
            }
            $select_flag=I('post.select_flag','','string');
            if($select_flag==0)
            {
                $upload_dir=C('UPLOAD_SHANGPIN_DIR');
                $first_file  = $_FILES['photo'];          //获取文件1的信息
                if ($first_file['error'] == UPLOAD_ERR_OK)
                {
                    $temp_name = $first_file['tmp_name']; //上传文件1在服务器上的临时存放路径
                    if ($first_file['type'] == "image/png")
                    {
                        $filename1=getRadomFileName();
                        $filename1=$filename1.'.png';
                    }
                    else if ($first_file['type'] == "image/jpeg")
                    {
                        $filename1=getRadomFileName();
                        $filename1=$filename1.'.jpg';
                    }
                    move_uploaded_file($temp_name, iconv("UTF-8","gb2312",   $upload_dir.$filename1));
                    //移动临时文件夹中的文件1到存放上传文件的目录，并重命名为真实名称
                }
                else
                {
                    echo '[文件1]上传失败!<br/>';
                    return;
                }

                $shenfenzheng_image = $filename1;
                $goods_insert_data['goods_image'] =$shenfenzheng_image;
            }
            else  if($select_flag==1)
            {
                $goods_insert_data['goods_image'] =I('post.goods_image','','string');
            }
            $goods_insert_data['wbid']=$wbid;
            $goods_insert_data['goods_id']=D('Newproduct')->max('goods_id')+1;
            $goods_insert_data['type_id']=I('post.fenlei','','string');
            $goods_insert_data['goods_name']=$one_goods_name;
            $goods_insert_data['goods_pinyin']= getpinyin($one_goods_name);
            $goods_insert_data['goods_quanpin']=getAllPY($one_goods_name);
            $goods_insert_data['barcode']=I('post.barcode','','string');
            // $goods_insert_data['one_jian_num']=I('post.one_jian_num',1,'int');
            $goods_insert_data['shou_price']=I('post.shou_price','','string');
            $goods_insert_data['dtInsertTime']= $dtInsertTime;
            if(D('Newproduct')->add($goods_insert_data)===false)
            {
                $result=false;
            }
            if($result)
            {
                D()->commit();    //提交
                $data['status']=1;
            }
            else
            {
                D()->rollback();    //回滚
                $data['status']=-1;
            }
            $this->ajaxReturn($data);
        }
    }
    public function shangpin_edit()
    {
        $bEdit=0; 
        $wbid=session('wbid');
        $goods_id=I('get.goods_id',0,'int');
        $goods_info=D('Newproduct')->where(array('wbid'=>$wbid,'goods_id'=>$goods_id))->find();
        $goods_info['shou_price'] =sprintf("%.2f",$goods_info['shou_price']);
        if(empty($goods_info['goods_image']))
        {
            $goods_info['goods_image'] =C('SHANGPIN_TUPIAN_PATH_URL').'moren.png';
        }
        else
        {
            $path =C('UPLOAD_SHANGPIN_DIR').$goods_info['goods_image'];
            if(file_exists($path))
            {
                $goods_info['goods_image'] =C('SHANGPIN_TUPIAN_PATH_URL').$goods_info['goods_image'];
            }
            else
            {
                $goods_info['goods_image'] =C('SHANGPIN_TUPIAN_PATH_URL').'moren.png';
            }
        }


        $this->assign('goods_info',$goods_info);
        $type_list=D('ProductType')->select();


        if($goods_info['is_zuhe']==1)
        {
            $bEdit=1;//不允许修改商品价格
        }
        else  if($goods_info['is_zuhe']==0)
        {
            $nowtime=date('Y-m-d H:i:s',time());
            $lastshiftinfo= D('Newproductjb')->where(array('wbid'=>$wbid))->order('dtEndTime desc')->limit(1)->find();
            $lastshifttime=date('Y-m-d H:i:s',strtotime($lastshiftinfo['dtEndTime']));

            //如果本班该商品已经单卖过，不允许组合
            $map1=array();
            $map1['wbid']=$wbid;
            $map1['dtInsertTime']=array('BETWEEN',array($lastshifttime,$nowtime));
            $map1['goods_id']=$goods_id;
            $bXiaoshou= D('Newproductxsmx')->where($map1)->find();
            if(!empty($bXiaoshou))
            {
               $bEdit=1;  //本班已销售过  禁止修改价格
            }
            else
            {
              $bEdit=0; 
            }
        }    

  
        $this->assign('bEdit',$bEdit);
        $this->assign('type_list',$type_list);
        $this->display();
    }
    public function shangpin_edit_set()
    {
        $wbid=session('wbid');
        if(IS_AJAX)
        {
            $data['status']=-1;
            $one_goods_name=I('post.goods_name','','string');
            $goods_id=I('post.goods_id',0,'int');

            $old_goods_name= D('Newproduct')->where(array('wbid'=>session('wbid'),'goods_id'=>$goods_id))->getField('goods_name');
            if($one_goods_name!=$old_goods_name )
            {
                if(D('Newproduct')->where(array('wbid'=>session('wbid'),'goods_name'=>$one_goods_name))->find())
                {
                    $data['status']=-2;
                    $this->ajaxReturn($data);
                    return;
                }
            }

            $select_flag=I('post.select_flag','','string');
            if($select_flag==1)
            {

            }
            else if($select_flag==0)
            {
                $upload_dir=C('UPLOAD_SHANGPIN_DIR');
                $first_file  = $_FILES['photo'];          //获取文件1的信息
                if ($first_file['error'] == UPLOAD_ERR_OK)
                {
                    $temp_name = $first_file['tmp_name']; //上传文件1在服务器上的临时存放路径

                    if ($first_file['type'] == "image/png")
                    {
                        $filename1=getRadomFileName();
                        $filename1=$filename1.'.png';
                    }
                    else if ($first_file['type'] == "image/jpeg")
                    {
                        $filename1=getRadomFileName();
                        $filename1=$filename1.'.jpg';
                    }
                    move_uploaded_file($temp_name, iconv("UTF-8","gb2312",   $upload_dir.$filename1));
                    //移动临时文件夹中的文件1到存放上传文件的目录，并重命名为真实名称
                }
                else
                {
                    echo '[文件1]上传失败!<br/>';
                    return;
                }
                $shenfenzheng_image = $filename1;
                $goods_update_data['goods_image'] =$shenfenzheng_image;
            }
            $goods_update_data['type_id']=I('post.fenlei','','string');
            $goods_update_data['barcode']=I('post.barcode','','string');
            $goods_update_data['one_jian_num']=I('post.one_jian_num','1','string');
            $goods_update_data['shou_price']=I('post.shou_price',0,'float');
            $goods_update_data['goods_name']=$one_goods_name;
            $goods_update_data['goods_pinyin']= getpinyin($one_goods_name);
            $goods_update_data['goods_quanpin']=getAllPY($one_goods_name);

            if(!empty($wbid))
            {
                $goods_update_result=D('Newproduct')->where(array('wbid'=>$wbid,'goods_id'=>$goods_id))->save($goods_update_data);
                if(!empty($goods_update_result))
                {
                    $data['status']=1;
                }
                else
                {
                    $data['status']=-1;
                }

            }
            $this->ajaxReturn($data);

        }
    }
    public function shangpin_delete_set()
    {
        $wbid=session('wbid');
        if(IS_AJAX)
        {
            $goods_id=I('post.goods_id',0,'int');
            $data['status']=-1;
            if((!empty($wbid)) &&(!empty($goods_id)))
            {
                //判断下 如果该商品有单据，就不能删除

                $goodsinfo = D('Newproduct')->where(array('wbid'=>$wbid,'goods_id'=>$goods_id))->find();
                if(!empty($goodsinfo['ck_num']) || !empty($goodsinfo['kc_num']))
                {
                    $data['status']=-2;
                    $this->ajaxReturn($data);
                    return;
                }



                $nowtime=date('Y-m-d H:i:s',time());
                $lastshiftinfo= D('Newproductjb')->where(array('wbid'=>$wbid))->order('dtEndTime desc')->limit(1)->find();
                $lastshifttime=date('Y-m-d H:i:s',strtotime($lastshiftinfo['dtEndTime']));

                //如果本班该商品已经单卖过，不允许组合
                $map1=array();
                $map1['wbid']=$wbid;
                $map1['dtInsertTime']=array('BETWEEN',array($lastshifttime,$nowtime));
                $map1['goods_id']=$goods_id;
                $bXiaoshou= D('Newproductxsmx')->where($map1)->find();
                if(!empty($bXiaoshou))
                {
                    $data['status']=-3;
                    $this->ajaxReturn($data);
                    return;
                }


                $goods_delete_result=D('Newproduct')->where(array('wbid'=>$wbid,'goods_id'=>$goods_id))->setField('deleted',1);
                if(!empty($goods_delete_result))
                {
                    $data['status']=1;
                }
                else
                {
                    $data['status']=-1;
                }
            }
            $this->ajaxReturn($data);

        }
    }

    //======================================商品信息页面结束==========================================

//===========================================商品信息结束==================================
    //======================================组合管理开始======================================
    public  function zuhe(){
        $wbid=session('wbid');
        $goods_id=I('get.goods_id','','string');
        $map = array();
        $map['wbid']=$wbid;
        $map['goods_id']=$goods_id;
        $goodsinfo=D('Newproduct')->where($map)->find();

        //print_r(D('Newproduct')->getLastSql());
        // return;


        $this->assign('goodsinfo',$goodsinfo);

        $zuhelist=D('Newzuhe')->where(array('wbid'=>$wbid,'deleted'=>0))->select();
        $this->assign('zuhelist',$zuhelist);
        $this->assign('zuhe_list_price',json_encode($zuhelist));

        creatToken();
        $this->display();
    }
    public  function zuhe_add_set(){
        header('Access-Control-Allow-Origin:*');
        $zuhe_name=I('get.zuhe_name','','string');
        $zuhe_price=I('get.zuhe_price','0','float');
        $zuhe_type=I('get.zuhe_type','1','float');
        $wbid=session('wbid');
        $map=array();
        $map['wbid']=$wbid;
        $map['zuhe_name']=$zuhe_name;
        $map['deleted']=0;
        $bFind=D('Newzuhe')->where($map)->find();
        if($bFind)
        {
            $data['result']=-2;
        }
        else
        {
            $result=true;
            D()->startTrans();
            $maxid=D('Newproduct')->where(array('wbid'=>$wbid))->max('goods_id');
            if(empty($maxid))
            {
                $maxid=1;
            }
            $zuhe_id=$maxid+1;
            $zuhe_insert_data=array();
            $zuhe_insert_data['zuhe_name']=$zuhe_name;
            $zuhe_insert_data['zuhe_price']=$zuhe_price;
            $zuhe_insert_data['zuhe_id']=$zuhe_id;
            $zuhe_insert_data['zuhe_type']=$zuhe_type;
            $zuhe_insert_data['wbid']=$wbid;
            $zuhe_insert_data['isValid']=0;
            if(D('Newzuhe')->add($zuhe_insert_data)===false)
            {
                $result=false;
            }
            $goods_insert_data=array();
            $goods_insert_data['type_id']=1;
            $goods_insert_data['goods_id']=$zuhe_id;
            $goods_insert_data['is_zuhe']=2;
            $goods_insert_data['zuhe_id']=0;
            $goods_insert_data['type_id']=$zuhe_type;
            $goods_insert_data['goods_name']=$zuhe_name;
            $goods_insert_data['goods_pinyin']= getpinyin($zuhe_name);
            $goods_insert_data['goods_quanpin']=getAllPY($zuhe_name);
            $goods_insert_data['shou_price']=$zuhe_price;
            $goods_insert_data['wbid']=$wbid;
            $goods_insert_data['dtInsertTime']=date('Y-m-d H:i:s',time());
            if(D('Newproduct')->add($goods_insert_data)===false)
            {
                $result=false;
            }
            if($result)
            {
                D()->commit();
                $data['result']=1;
            }
            else
            {
                D()->rollback();
                $data['result']=-1;
            }
        }
        $this->ajaxReturn($data);
    }
    public  function zuhe_delete_set(){
        header('Access-Control-Allow-Origin:*');
        $zuhe_id=I('post.zuhe_id','','string');

        $bFind=false;

        $wbid=session('wbid');
        $map=array();
        $map['wbid']=$wbid;
        $map['zuhe_id']=$zuhe_id;
        $bFind=D('Newzuhe')->where($map)->find();

        if(empty($bFind))
        {
            $data['result']=-3;
            $this->ajaxReturn($data);
            return;
        }

        $map=array();
        $map['wbid']=$wbid;
        $map['zuhe_id']=$zuhe_id;
        $map['is_zuhe']=1;
        $bFind1=D('Newproduct')->where($map)->find();

        if(!empty($bFind1))
        {
            $data['result']=-2;
            $this->ajaxReturn($data);
            return;
        }



        $result=true;
        D()->startTrans();
        $map=array();
        $map['wbid']=$wbid;
        $map['zuhe_id']=$zuhe_id;

        if(D('Newzuhe')->where($map)->setField('deleted',1)===false)
        {
            $result=false;
        }

        $map=array();
        $map['wbid']=$wbid;
        $map['goods_id']=$zuhe_id;

        if(D('Newproduct')->where($map)->setField('deleted',1)===false)
        {
            $result=false;
        }

        if($result)
        {
            D()->commit();
            $data['result']=1;
        }
        else
        {
            D()->rollback();
            $data['result']=-1;
        }

        $this->ajaxReturn($data);
    }
    public  function zuhe_edit(){
        $zuhe_id=I('get.zuhe_id','','string');
        $wbid=session('wbid');
        $map=array();
        $map['wbid']=$wbid;
        $map['zuhe_id']=$zuhe_id;
        $zuheinfo=D('Newzuhe')->where($map)->find();
        $this->assign('zuheinfo',$zuheinfo);
        $this->display();
    }
    public  function zuhe_edit_set(){
        header('Access-Control-Allow-Origin:*');
        $zuhe_id=I('post.zuhe_id','','string');
        $zuhe_name=I('post.zuhe_name','','string');
        $zuhe_type=I('post.zuhe_type','','string');
        $bFind=false;
        $wbid=session('wbid');
        $map=array();
        $map['wbid']=$wbid;
        $map['zuhe_id']=$zuhe_id;
        $bFind=D('Newzuhe')->where($map)->find();
        if(empty($bFind))
        {
            $data['result']=-2;
            $this->ajaxReturn($data);
            return;
        }
        $result=true;
        D()->startTrans();
        $map=array();
        $map['wbid']=$wbid;
        $map['zuhe_id']=$zuhe_id;
        $zuhe_update_data['zuhe_name']=$zuhe_name;
        $zuhe_update_data['zuhe_type']=$zuhe_type;
        if(D('Newzuhe')->where($map)->save($zuhe_update_data)===false)
        {
            $result=false;
        }
        $map=array();
        $map['wbid']=$wbid;
        $map['goods_id']=$zuhe_id;
        $goods_update_data['goods_name']=$zuhe_name;
        $goods_update_data['type_id']=$zuhe_type;
        if(D('Newproduct')->where($map)->save($goods_update_data)===false)
        {
            $result=false;
        }
        if($result)
        {
            D()->commit();
            $data['result']=1;
        }
        else
        {
            D()->rollback();
            $data['result']=-1;
        }
        $this->ajaxReturn($data);
    }
    public  function zuheguanli(){
        $this->display();
    }
    public  function getallzuhelist(){
        if(IS_AJAX)
        {
            $wbid=session('wbid');
            $page = I('get.page',1,'int');
            $rows = I('get.rows',20,'int');
            $sord = I('get.sord','','string')?:'desc';
            $sidx = I('get.sidx','','string')?:'id';
            $map = array();
            $map['wbid']=$wbid;
            $map['deleted']=0;
            $count= D('Newzuhe')->getZuheListByMap_Count($map);
            $sql_page=ceil($count/$rows);
            if($page>$sql_page) $page=1;
            $wblist = D('Newzuhe')->getZuheListByMap($map,"$sidx $sord",$page,$rows);
            $response = new \stdClass();
            $response->records = $wblist['count'];
            $response->page = $page;
            $response->total = ceil($wblist['count'] / $rows);
            foreach($wblist['list'] as $key => $value)
            {
                $response->rows[$key]['id'] = $key;
                $response->rows[$key]['cell'] = $value;
            }
            $this->ajaxReturn($response);
        }

    }


    //===================新增一个商品到组合===========================
    public  function addgoods_to_zuhe_set()
    {
        header('Access-Control-Allow-Origin:*');
        $zuhe_id=I('get.zuhe_id','','string');
        $goods_id=I('get.goods_id','','string');


        if(!checkToken($_GET['token']))
        {
            writelog('addgoods_to_zuhe_set---重复提交');
            $data['result']=-4;
            $this->ajaxReturn($data);
            return;
        }
        else
        {
            //writelog('addgoods_to_zuhe_set---未重复提交');
        }



        $wbid=session('wbid');
        $goodsinfo=D('Newproduct')->field('ck_num,kc_num,goods_name')->where(array('wbid'=>$wbid,'goods_id'=>$goods_id))->find();
        $zuhe_goods_name=D('Newproduct')->where(array('wbid'=>$wbid,'goods_id'=>$zuhe_id))->getField('goods_name');

        if(empty($zuhe_id) || empty($goods_id))
        {
            $data['result']=-2;
            $this->ajaxReturn($data);
            return;
        }

        $nowtime=date('Y-m-d H:i:s',time());
        $lastshiftinfo= D('Newproductjb')->where(array('wbid'=>$wbid))->order('dtEndTime desc')->limit(1)->find();
        $lastshifttime=date('Y-m-d H:i:s',strtotime($lastshiftinfo['dtEndTime']));

        //如果本班该商品已经单卖过，不允许组合
        $map1=array();
        $map1['wbid']=$wbid;
        $map1['dtInsertTime']=array('BETWEEN',array($lastshifttime,$nowtime));
        $map1['goods_id']=$goods_id;
        $bXiaoshou= D('Newproductxsmx')->where($map1)->find();
        if(!empty($bXiaoshou))
        {
            $data['result']=-3;
            $this->ajaxReturn($data);
            return;
        }

        D()->startTrans();
        $result=true;
        //添加一条总上架记录
        $post_order_no = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);  //网站订单号
        $post_order_no='ZH'.$post_order_no;
        $dtInsertTime=date('Y-m-d H:i:s',time());
        $goods_update_data=array();
        $goods_update_data['is_zuhe']=1;
        $goods_update_data['zuhe_id']=$zuhe_id;
        $goods_update_data['kc_num']=0;
        if(D('Newproduct')->where(array('wbid'=>$wbid,'goods_id'=>$goods_id))->save($goods_update_data)===false)
        {
            $result=false;
            writelog('-----Newproduct----0  error--');
        }


        //更新该商品组合的货架库存
        if(D('Newproduct')->where(array('wbid'=>$wbid,'goods_id'=>$zuhe_id))->setInc('kc_num',$goodsinfo['kc_num'])===false)
        {
            $result=false;
            writelog('-----Newproduct----1  error--');
        }


        if(D('Newproduct')->where(array('wbid'=>$wbid,'goods_id'=>$zuhe_id))->setInc('childgoodsnum',1)===false)
        {
            $result=false;
            writelog('-----Newproduct----2  error--');
        }

       
        //添加一条组合商品 上架明细记录
        $shangxiajiamx_insert_data=array();
        $shangxiajiamx_insert_data['goods_id']=$zuhe_id;
        $shangxiajiamx_insert_data['num']     =$hj_num;
        $shangxiajiamx_insert_data['ck_num']  =0;
        $shangxiajiamx_insert_data['hj_num']  =$hj_num;
        $shangxiajiamx_insert_data['post_order_no']=$post_order_no;
        $shangxiajiamx_insert_data['wbid']=$wbid;
        $shangxiajiamx_insert_data['operate']=session('username');
        $shangxiajiamx_insert_data['dtInsertTime']=$dtInsertTime;
        $shangxiajiamx_insert_data['zuhe_id']=0;
        $shangxiajiamx_insert_data['is_zuhe_goods']=2;
        $shangxiajiamx_insert_data['zuhe_flag']=1;
        $shangxiajiamx_insert_data['ordertype']=3;
        if(D('Newproductsxjmx')->add($shangxiajiamx_insert_data)===false)
        {
            $result=false;
            writelog('-----Newproduct----2  error--');
        }


        $shangxiajia_insert_data=array();
        $shangxiajia_insert_data['post_order_no']=$post_order_no;
        $shangxiajia_insert_data['shangxia_status']=0;
        $shangxiajia_insert_data['wbid']=$wbid;
        $shangxiajia_insert_data['info']='货架数量:'.$goodsinfo['goods_name'].' -'.$hj_num.'个 '.$zuhe_goods_name.' (组合)+'.$hj_num.'个';
        $shangxiajia_insert_data['dtInsertTime']=$dtInsertTime;
        $shangxiajia_insert_data['operator']=session('username');
        $shangxiajia_insert_data['bz']='将商品 '.$goodsinfo['goods_name'].'组合到'.$zuhe_goods_name;
        $shangxiajia_insert_data['detailinfo']='';
        $shangxiajia_insert_data['zuhe_flag']=1;
        if(D('Newproductsxj')->add($shangxiajia_insert_data)===false)
        {
            $result=false;
            writelog('-----Newproduct----3  error--');
        }

        //更新组合库 里的商品列表
        $old_goodsid_str=D('Newzuhe')->where(array('zuhe_id'=>$zuhe_id,'wbid'=>$wbid))->getField('goodsid_list');
        $zuhe_update_data=array();
        $zuhe_update_data['goodsid_list']=$old_goodsid_str.$goods_id.',';
        $zuhe_update_data['isValid']=1;
        if(D('Newzuhe')->where(array('zuhe_id'=>$zuhe_id,'wbid'=>$wbid))->save($zuhe_update_data)===false)
        {
            $result=false;
            writelog('-----Newproduct----4  error--');
        }
        


        if($result)
        {
            D()->commit();
            $data['result']=1;
        }else
        {
            D()->rollback();
            $data['result']=-1;
        }
        $this->ajaxReturn($data);
    }
    //=================================拆分一个商品=================================
    public function chaifen()
    {
        $goods_id=I('get.goods_id','','string');
        $zuhe_id=I('get.zuhe_id','','string');
        $this->assign('zuhe_id',$zuhe_id);
        $this->assign('goods_id',$goods_id);
        $disabl=I('get.disabl','','string');
        $this->assign('disabl',$disabl);

        $zuheinfo=D('Newproduct')->field('goods_name,kc_num')->where(array('wbid'=>session('wbid'),'goods_id'=>$zuhe_id))->find();
        $this->assign('zh_name',$zuheinfo['goods_name']);
        $sumnum=$zuheinfo['kc_num'];
        if(empty($sumnum))
        {
            $sumnum=0;
        }

        $single_num=D('Newproduct')->where(array('wbid'=>session('wbid'),'zuhe_id'=>$zuhe_id,'is_zuhe'=>1))->count();

      // print_r(D('Newproduct')->getLastSql());
     //   return;
        if($single_num>1)
        {
           $disabl=0;
        }
        else
        {
           $disabl=1;  //不能输入
        }    

        $this->assign('disabl',$disabl);

        $this->assign('sumnum',$sumnum);
        creatToken();
        $this->display();
    }
    public  function chafengoods_from_zuhe_set()
    {
        header('Access-Control-Allow-Origin:*');
        $goods_num=I('get.goods_num','0','string');
        $goods_id=I('get.goods_id','','string');
        $zuhe_id=I('get.zuhe_id','','string');

        if(empty($zuhe_id) || empty($goods_id))
        {
            $data['result']=-2;
            $this->ajaxReturn($data);
            return;
        }
        if(!checkToken($_GET['token']))
        {
            writelog('chafengoods_from_zuhe_set---重复提交');
            $data['status']=-2;
            $this->ajaxReturn($data);
            return;
        }

        $wbid=session('wbid');
        //添加一条总上架记录
        $post_order_no = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);  //网站订单号
        $post_order_no='CF'.$post_order_no;
        $dtInsertTime=date('Y-m-d H:i:s',time());

        $goods_info=D('Newproduct')->field('goods_name,kc_num,ck_num')->where(array('wbid'=>$wbid,'goods_id'=>$goods_id))->find();
        $zuhe_goods_info=D('Newproduct')->field('goods_name,kc_num,ck_num')->where(array('wbid'=>$wbid,'goods_id'=>$zuhe_id))->find();

        $old_zuhe_hj_num=$zuhe_goods_info['kc_num'];
        if($old_zuhe_hj_num < $goods_num)
        {
            $data['result']=-3;
            $this->ajaxReturn($data);
            return;
        }

        $result=true;
        D()->startTrans();
        /*
         if($goods_num >0)
         {
             //如果是拆分最后一个商品的话，拆分数量就是组合商品的总库存
             $shuliang=D('Newproduct')->where(array('wbid'=>$wbid,'goods_id'=>$zuhe_id,'is_zuhe'=>1,'deleted'=>0))->count();
             if($shuliang==1)
             {
                 $goods_num= $old_zuhe_hj_num;
                 //如果该商品是最后一个组合商品的话,组合数量必须全给此商品，并且
                 $zuhe_update_data=array();
                 $zuhe_update_data['isValid']=0;
                 if(D('Newzuhe')->where(array('wbid'=>$wbid,'goods_id'=>$zuhe_id,'deleted'=>0))->save($zuhe_update_data)===false)
                 {
                     $result=false;
                 }
             }
         }
        */


        if(D('Newproduct')->where(array('wbid'=>$wbid,'goods_id'=>$zuhe_id))->setDec('childgoodsnum',1)===false)
        {
            $result=false;
            writelog('-----Newproduct----1  error--');
        }
        

        //1.更新商品信息表 该商品的信息
        if($goods_num == 0)
        {
            $goods_update_data=array();
            $goods_update_data['is_zuhe']=0;
            $goods_update_data['zuhe_id']=0;
            if(D('Newproduct')->where(array('wbid'=>$wbid,'goods_id'=>$goods_id))->save($goods_update_data)===false)
            {
                $result=false;
                writelog('-----Newproductsxj----0--1--  error--');
            }
        }
        else if($goods_num > 0)
        {
            //添加一条组合商品 下架一部分数量 明细记录
            $goods_update_data=array();
            $goods_update_data['is_zuhe']=0;
            $goods_update_data['zuhe_id']=0;
            if(D('Newproduct')->where(array('wbid'=>$wbid,'goods_id'=>$goods_id))->save($goods_update_data)===false)
            {
                $result=false;
                writelog('-----Newproductsxj----0-2-  error--');
            }

            $shangxiajiamx_insert_data=array();
            $shangxiajiamx_insert_data['goods_id']=$zuhe_id;
            $shangxiajiamx_insert_data['num']     =$goods_num;
            $shangxiajiamx_insert_data['ck_num']  =0;
            $shangxiajiamx_insert_data['hj_num']  =$goods_info['kc_num'];
            $shangxiajiamx_insert_data['post_order_no']=$post_order_no;
            $shangxiajiamx_insert_data['shangxia_status']=1;             //下架
            $shangxiajiamx_insert_data['wbid']=$wbid;
            $shangxiajiamx_insert_data['operate']=session('username');
            $shangxiajiamx_insert_data['dtInsertTime']=$dtInsertTime;
            $shangxiajiamx_insert_data['zuhe_id']=0;
            $shangxiajiamx_insert_data['is_zuhe_goods']=2;
            $shangxiajiamx_insert_data['zuhe_flag']=1;
            $shangxiajiamx_insert_data['ordertype']=4;
            if(D('Newproductsxjmx')->add($shangxiajiamx_insert_data)===false)
            {
                $result=false;
                writelog('-----Newproductsxj----1  error--');
            }

            //添加一条单个商品 上架一部分数量 明细记录
            $shangxiajiamx_insert_data=array();
            $shangxiajiamx_insert_data['goods_id']=$goods_id;
            $shangxiajiamx_insert_data['num']     =$goods_num;
            $shangxiajiamx_insert_data['ck_num']  =$goods_info['ck_num'];
            $shangxiajiamx_insert_data['hj_num']  =0;
            $shangxiajiamx_insert_data['post_order_no']=$post_order_no;
            $shangxiajiamx_insert_data['shangxia_status']=0;             //上架
            $shangxiajiamx_insert_data['wbid']=$wbid;
            $shangxiajiamx_insert_data['operate']=session('username');
            $shangxiajiamx_insert_data['dtInsertTime']=$dtInsertTime;
            $shangxiajiamx_insert_data['zuhe_id']=0;
            $shangxiajiamx_insert_data['is_zuhe_goods']=0;
            $shangxiajiamx_insert_data['zuhe_flag']=1;
            $shangxiajiamx_insert_data['ordertype']=4;
            if(D('Newproductsxjmx')->add($shangxiajiamx_insert_data)===false)
            {
                $result=false;
                writelog('-----Newproductsxj----2  error--');
            }
            $shangxiajia_insert_data['post_order_no']=$post_order_no;
            $shangxiajia_insert_data['shangxia_status']=0;
            $shangxiajia_insert_data['wbid']=$wbid;
            $shangxiajia_insert_data['info']='货架数量:'.$zuhe_goods_info['goods_name'].'(组合) -'.$goods_num.'个  '.$goods_info['goods_name'].'+'.$goods_num.'个';
            $shangxiajia_insert_data['dtInsertTime']=$dtInsertTime;
            $shangxiajia_insert_data['operator']=session('username');
            $shangxiajia_insert_data['bz']='将'.$zuhe_goods_info['goods_name'].'(组合)拆分到'.$goods_info['goods_name'];
            $shangxiajia_insert_data['detailinfo']='';
            $shangxiajia_insert_data['zuhe_flag']=1;
            if(D('Newproductsxj')->add($shangxiajia_insert_data)===false)
            {
                $result=false;
                writelog('-----Newproductsxj----3  error--');
            }
            //2.更新该组合商品的货架库存数量
            if(D('Newproduct')->where(array('wbid'=>$wbid,'goods_id'=>$zuhe_id))->setDec('kc_num',$goods_num)===false)
            {
                $result=false;
                writelog('-----Newproductsxj----4  error--');
            }
            //3.更新拆分出来的商品货架库存数量
            if(D('Newproduct')->where(array('wbid'=>$wbid,'goods_id'=>$goods_id))->setInc('kc_num',$goods_num)===false)
            {
                $result=false;
                writelog('-----Newproductsxj----5  error--');
            }

        }
        if($result)
        {
            D()->commit();
            $data['result']=1;
        }
        else
        {
            D()->rollback();
            $data['result']=-1;
        }

        $this->ajaxReturn($data);
    }
    //======================================组合管理结束=================================================
    
    //===================================单据中心 =================================================
    
        //进出货统计
    public function getjchtongjilist()
    {
        if(IS_AJAX)
        {
            $page = I('get.page',1,'int');
            $rows = I('get.rows',20,'int');
            $sord = I('get.sord','','string')?:'desc';
            $sidx = I('get.sidx','','string')?:'dtInsertTime';

            $goods_name    = I('get.goods_name','','string');
            $jch_position  = I('get.jch_position','','string');  
            $post_order_no = I('get.cardno','','string');   
            $daterange     = I('get.daterange','','string');            
            
            
                            
            $map = array();     
            $map['wbid']=session('wbid');   


  
     
            
            if(!empty($goods_name))
            {
              $map['info']=array('LIKE','%'.$goods_name.'%');
            }  
            
            if(!empty($post_order_no))
            {
              $map['post_order_no']=array('LIKE','%'.$post_order_no.'%');
            }  
            
            if(!empty($daterange))  
            {
              list($start,$end) = explode(' - ',$daterange);    
              $start = str_replace('/','-',$start);            
              $end = str_replace('/','-',$end);                
              $map['dtInsertTime'] = array('BETWEEN',array($start,$end));
            }
            
            
            
            if($jch_position==1)
            {
                $map['jch_type']=1;
            }
            else if($jch_position==2)
            {
                $map['jch_type']=2;
            }
            
                
            $count= D('Newproductjch')->gejhtongjilistByMap_count($map);
            $sql_page=ceil($count/$rows);  
            if($page>$sql_page) $page=1;    
            $wblist = D('Newproductjch')->gejhtongjilistByMap($map,"$sidx $sord",$page,$rows);                                                         
            
            $response = new \stdClass();
            $response->records = $wblist['count'];
            $response->page = $page;
            $response->total = ceil($wblist['count'] / $rows);
            foreach($wblist['list'] as $key => $value)
            {       
              $response->rows[$key]['id'] = $key;
              $response->rows[$key]['cell'] = $value;
            }
            $this->ajaxReturn($response);
        }  
    }
    
    //进出货统计明细
    public function xq_chuhuomx()
    {
        $post_order_no=I('get.post_order_no','','string');
        $jch_type=I('get.jch_type','','string');

        session('post_order_no',$post_order_no);
        session('jch_type',$jch_type);
        $this->display();
    }
    public function xq_jinhuomx()
    {
        $post_order_no=I('get.post_order_no','','string');
        session('post_order_no',$post_order_no);

        $jch_type=I('get.jch_type','','string');
        session('jch_type',$jch_type);

        $this->display();
    }
    
    public function getjch_mx_listByMap()
    {
        if(IS_AJAX)
        {
            $page = I('get.page',1,'int');
            $rows = I('get.rows',20,'int');
            $sord = I('get.sord','','string')?:'desc';
            $sidx = I('get.sidx','','string')?:'dtInsertTime';



            $map = array();
            $map['jhmx.wbid']=session('wbid');
            $map['jhmx.post_order_no']= session('post_order_no');
           // $map['jhmx.jch_type']= session('jch_type');



            $count= D('Newproductjchmx')->getjhtongji_mx_listByMap_count($map);

            $sql_page=ceil($count/$rows);
            if($page>$sql_page) $page=1;
            $wblist = D('Newproductjchmx')->getjhtongji_mx_listByMap($map,"$sidx $sord",$page,$rows);
          // print_r(D('Newproductjchmx')->getLastSql());
          // return;

            $response = new \stdClass();
            $response->records = $wblist['count'];
            $response->page = $page;
            $response->total = ceil($wblist['count'] / $rows);
            foreach($wblist['list'] as $key => $value)
            {
                $response->rows[$key]['id'] = $key;
                $response->rows[$key]['cell'] = $value;
            }
            $this->ajaxReturn($response);
        }
    }
    
    
    


    //==================上下架统计============
    public function get_shangxiajia_tongji_list()
    {
        if(IS_AJAX)
        {
            $page = I('get.page',1,'int');
            $rows = I('get.rows',20,'int');
            $sord = I('get.sord','','string')?:'desc';
            $sidx = I('get.sidx','','string')?:'dtInsertTime';

            $goods_name    = I('get.goods_name','','string');
            $position      = I('get.position',0,'int');   
            $post_order_no = I('get.cardno2','','string'); 
            $daterange     = I('get.daterange2','','string'); 
            
            
            
                     
            $map = array(); 
    
            $map['wbid']=session('wbid');
            
        
            if(!empty($position))
            {
          //    $map['shangxia_status']=(int)$position-1;
            }  
            
            if(!empty($post_order_no))
            {
              $map['post_order_no']=array('LIKE','%'.$post_order_no.'%');
            } 
            
            if(!empty($daterange))  
            {
              list($start,$end) = explode(' - ',$daterange);    
              $start = str_replace('/','-',$start);            
              $end = str_replace('/','-',$end);                
              $map['dtInsertTime'] = array('BETWEEN',array($start,$end));
            }
                    
        
            $count= D('Newproductsxj')->getsxjtongjiListByMap_count($map);
            $sql_page=ceil($count/$rows);  
            if($page>$sql_page) $page=1;    
            $wblist = D('Newproductsxj')->getsxjtongjiListByMap($map,"$sidx $sord",$page,$rows);     
            
            $response = new \stdClass();
            $response->records = $wblist['count'];
            $response->page = $page;
            $response->total = ceil($wblist['count'] / $rows);
            foreach($wblist['list'] as $key => $value)
            {       
              $response->rows[$key]['id'] = $key;
              $response->rows[$key]['cell'] = $value;
            }
            $this->ajaxReturn($response);
        }  
    }


    public function xq_shangjiamx()
    {
        $post_order_no=I('get.post_order_no','','string');
        session('post_order_no',$post_order_no); 
        $this->display();
    }
    public function xq_xiajiamx()
    {
        $post_order_no=I('get.post_order_no','','string');
        session('post_order_no',$post_order_no); 
        $this->display();
    }

    
    
    public function get_shangxiajia_mx_list()
    {
        $wbid = session('wbid');
        $post_order_no = session('post_order_no');
        $type_list = D('ProductType')->select();

        $list = array();
        $map = array();
        $map['wbid'] = $wbid;
        $map['post_order_no'] = $post_order_no;
        $zuhe_flag = D('Newproductsxj')->where($map)->getField('zuhe_flag');
        
        if ($zuhe_flag == 0) //兼容原来的查询
        {
            $map = array();
            $map['info.wbid'] = $wbid;
            $map['sxjmx.post_order_no'] = $post_order_no;
            $list = D('Newproductsxjmx')->getsxj_mx_ListByMap2($map);
        }
        else if ($zuhe_flag == 1) 
        {
            //1.获取实际卖的商品列表 is_zuhe=0
            $map = array();
            $map['info.wbid'] = $wbid;
            $map['sxjmx.post_order_no'] = $post_order_no;
            $map['sxjmx.is_zuhe_goods'] = array('neq', 1);
            $shiji_order_list0 = D('Newproductsxjmx')->getsxj_mx_ListByMap2($map);



              //2.实际卖的商品列表   is_zuhe=1
            
            $map = array();
            $map['info.wbid'] = $wbid;
            $map['sxjmx.post_order_no'] = $post_order_no;
            $map['sxjmx.is_zuhe_goods'] = 1;
            $shiji_order_list1 = D('Newproductsxjmx')->getsxj_mx_ListByMap2($map);
            
          
          //循环遍历 非组合实际商品  和虚拟组合商品列表
            
            foreach ($shiji_order_list0 as &$val) 
            {
                foreach ($type_list as $val2) 
                {
                    if ($val2['type_id'] == $val['type_id']) 
                    {
                        $val['type_name'] = $val2['type_name'];
                        break;
                    }
                }
                if ($val['is_zuhe_goods'] == 2) 
                {
                    $zuhe_id = $val['goods_id'];
                    $i = 0;
                    $zuhe_goods_array = array();

                    foreach ($shiji_order_list1 as &$val1) {
                        if ($zuhe_id == $val1['zuhe_id']) {
                            $zuhe_goods_array[$i]['goods_id'] = $val1['goods_id'];
                            foreach ($type_list as $val2) {
                                if ($val2['type_id'] == $val['type_id']) {
                                    $list[$i]['type_name'] = $val2['type_name'];
                                    break;
                                }
                            }
                            $zuhe_goods_array[$i]['goods_name'] = $val1['goods_name'];
                            $zuhe_goods_array[$i]['unit'] = $val1['unit'];
                            $zuhe_goods_array[$i]['guige'] = $val1['guige'];
                            $zuhe_goods_array[$i]['shou_price'] = $val1['shou_price'];
                            $zuhe_goods_array[$i]['je'] = $val1['je'];
                            $zuhe_goods_array[$i]['is_zuhe'] = 1;
                            $zuhe_goods_array[$i]['zuhe_id'] = $val1['zuhe_id'];
                            $zuhe_goods_array[$i]['num'] = $val1['num'];
                            $zuhe_goods_array[$i]['hj_num'] = $val1['hj_num'];
                            $zuhe_goods_array[$i]['ck_num'] = $val1['ck_num'];
                            $i++;

                        }
                    }
                    $val['zuhelist'] = $zuhe_goods_array;
                }
            }
            
            $list=$shiji_order_list0;
        }
        $this->ajaxReturn($list);
    }   

    
    
    public function getxstongjilist()
    {
        if(IS_AJAX)
        {
            $page = I('get.page',1,'int');
            $rows = I('get.rows',20,'int');
            $sord = I('get.sord','','string')?:'desc';
            $sidx = I('get.sidx','','string')?:'dtInsertTime';

            $goods_name    = I('get.goods_name','','string');
            $position      = I('get.position',0,'int');   
            $post_order_no = I('get.cardno1','','string');
            $daterange     = I('get.daterange1','','string');
                                         
            $map = array();     
            $map['wbid']=session('wbid');               
            if(!empty($post_order_no))
            {
              $map['post_order_no']=array('LIKE','%'.$post_order_no.'%');
            }  
            
            if(!empty($goods_name))
            {
              $map['info']=array('LIKE','%'.$goods_name.'%');
            }  
            
            if(!empty($daterange))  
            {
              list($start,$end) = explode(' - ',$daterange);    
              $start = str_replace('/','-',$start);            
              $end = str_replace('/','-',$end);                
              $map['dtInsertTime'] = array('BETWEEN',array($start,$end));
            }
                                            
            $count= D('Newproductxs')->getxstjlistByMap_count($map);
            $sql_page=ceil($count/$rows);  
            if($page>$sql_page) $page=1;    
            $wblist = D('Newproductxs')->getxstjlistByMap($map,"$sidx $sord",$page,$rows); 
            
    
            
            $response = new \stdClass();
            $response->records = $wblist['count'];
            $response->page = $page;
            $response->total = ceil($wblist['count'] / $rows);
            foreach($wblist['list'] as $key => $value)
            {       
              $response->rows[$key]['id'] = $key;
              $response->rows[$key]['cell'] = $value;
            }
            $this->ajaxReturn($response);
        }  
    }


     

    /*
    public function getjbtongjilist()
    {
        if(IS_AJAX)
        {
            $page = I('get.page',1,'int');
            $rows = I('get.rows',20,'int');
            $sord = I('get.sord','','string')?:'desc';
            $sidx = I('get.sidx','','string')?:'dtInsertTime';

            $goods_name     = I('get.goods_name','','string');
            $position       = I('get.position',0,'int');   
            $post_order_no  = I('get.cardno4','','string');
            $daterange      = I('get.daterange4','','string');          
            
                        
            $map = array();    
            $map['wbid']=session('wbid');
                  
            if(!empty($position))
            {
              $map['position']=(int)$position-1;
            }  
            
            if(!empty($goods_name))
            {
              $map['info']=array('LIKE','%'.$goods_name.'%');
            }  
            if(!empty($post_order_no))
            {
              $map['post_order_no']=array('LIKE','%'.$post_order_no.'%');
            } 
            
            if(!empty($daterange))  
            {
              list($start,$end) = explode(' - ',$daterange);    
              $start = str_replace('/','-',$start);            
              $end = str_replace('/','-',$end);                
              $map['dtInsertTime'] = array('BETWEEN',array($start,$end));
            }
                        
            $count= D('Productjb')->getjbtongjilistByMap_count($map);
            $sql_page=ceil($count/$rows);  
            if($page>$sql_page) $page=1;    
            $wblist = D('Productjb')->getjbtongjilistByMap($map,"$sidx $sord",$page,$rows);     
            
            $response = new \stdClass();
            $response->records = $wblist['count'];
            $response->page = $page;
            $response->total = ceil($wblist['count'] / $rows);
            foreach($wblist['list'] as $key => $value)
            {       
              $response->rows[$key]['id'] = $key;
              $response->rows[$key]['cell'] = $value;
            }
            $this->ajaxReturn($response);
        }  
    }
    */

    public function getAllShangpinLiuchenglist()
    {
        if(IS_AJAX)
        {
            $page = I('get.page',1,'int');
            $rows = I('get.rows',10,'int');
            $sord = I('get.sord','','string')?:'desc';
            $sidx = I('get.sidx','','string')?:'dtInsertTime';
                        
            
        //  $daterange     = I('get.daterange5','','string');
            $goods_id     = I('get.goods_id','','string');
            $post_order_no     = I('get.order_num','','string');
            
            $map = array(); 
            $map['wbid']=session('wbid');   
            
            
            if(!empty($goods_id ))
            {
                $map['goods_id']=$goods_id; 
            }   
            if(!empty($post_order_no ))
            {
                $map['post_order_no']=$post_order_no;   
            }   
                                                                 
                                            
            $count= D('GoodsmxView')->getGoodsmxListByMap_count($map);
                    
            $sql_page=ceil($count/$rows);  
            if($page>$sql_page) $page=1;    
            $wblist = D('GoodsmxView')->getGoodsmxListByMap($map,"$sidx $sord",$page,$rows);        
            
            $response = new \stdClass();
            $response->records = $wblist['count'];
            $response->page = $page;
            $response->total = ceil($wblist['count'] / $rows);
            //$response->goods_name = $goods_name;
            foreach($wblist['list'] as $key => $value)
            {       
              $response->rows[$key]['id'] = $key;
              $response->rows[$key]['cell'] = $value;
            }
            $this->ajaxReturn($response);
            
        }  
    }


    public function getAllShangpinLiuchenglist2()
    {
        if(IS_AJAX)
        {
            $page = I('get.page',1,'int');
            $rows = I('get.rows',10,'int');
            $sord = I('get.sord','','string')?:'desc';
            $sidx = I('get.sidx','','string')?:'dtInsertTime';
                                 
            $daterange     = I('get.daterange5','','string');
            $goods_id     = I('get.goods_id','','string');
            $post_order_no     = I('get.order_num','','string');





            
            $map = array(); 
            $map['wbid']=session('wbid'); 


            if(!empty($daterange))  
            {
              list($start,$end) = explode(' - ',$daterange);    
              $start = str_replace('/','-',$start);            
              $end = str_replace('/','-',$end);                
              $map['dtInsertTime'] = array('BETWEEN',array($start,$end));
            }          



            $map1=array();
            $map1['wbid']=session('wbid');
            $map1['dtInsertTime']=array('gt',$start);
            $beg_goods_ck_str=D('Newproductjbkc')->where($map1)->order('dtInsertTime asc')->limit(1)->getField('detailinfo');                                         
                                            
            $count= D('Newproduct')->getProductinfoListByMap_count_zongzhang($map);
                    
            $sql_page=ceil($count/$rows);  
            if($page>$sql_page) $page=1;    
            $wblist = D('Newproduct')->getProductinfoListByMap_zongzhang($map,"$sidx $sord",$page,$rows,$beg_goods_ck_str);        
            
            $response = new \stdClass();
            $response->records = $wblist['count'];
            $response->page = $page;
            $response->total = ceil($wblist['count'] / $rows);
            //$response->goods_name = $goods_name;
            foreach($wblist['list'] as $key => $value)
            {       
              $response->rows[$key]['id'] = $key;
              $response->rows[$key]['cell'] = $value;
            }
            $this->ajaxReturn($response);
            
        }  
    }



    //===========================================单据中心结束========================================
    
}
