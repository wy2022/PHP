<?php
    namespace Home\Model;
    use Think\Model;
    class SpkcModel extends Model 
    {
        protected $tableName = 'Wspkc';


      public function getSpxscount($map=array()) //获取新闻
       {
             return $this->where($map)->count();
       }  

      public function getOneSpxsSum($spid) //获取新闻
       {
             return $this->where(array('Spid'=>$spid))->sum();
       }   

     
// select a.name ,a.spid,d.syid,a.unit,a.guige,a.price,sum(d.count)  from wspproductinfo a
// left join wspkc d on a.wb_id=d.wb_id and a.spid=d.spid 
// where  a.wb_id=5 
// group by a.name ,a.spid,a.unit,a.guige,a.price,d.syid  order by a.spid


      
      public function getSpkcList($map=array())    //传进来的数据map为array('BETWEEN',array($start,$end));
      {                                                                              //$condition['id'] = array(between,array('2001-1-1','2005-1-1'));相当于查询 where('id' between '2001-1-1' 

        $list = $this->join('LEFT JOIN WspProductInfo b ON wspkc.WB_ID=WspProductInfo.WB_ID and  wspkc.SpId=WspProductInfo.SpId')
        ->field('a.name ,a.spid,d.syid,a.unit,a.guige,a.price,sum(d.count) as count')->where($map)->group('a.name ,a.spid,a.unit,a.guige,a.price,d.syid')->select();
      
        return array('list'=>$list); 
      }
  }
