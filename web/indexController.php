<?php
/**
下载 http://github.com/zendframework/ZendSkeletonApplication 源码
替换index.phtml和indexController即可
*/
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Db\Sql;

class IndexController extends AbstractActionController
{
	public $ViewModel;

	function __construct($db = NULL)
	{
		$this->ViewModel = new ViewModel();
	}
	
    public function indexAction()
    {
        if($this->getRequest()->isXmlHttpRequest()) {
            $response = $this->getResponse();
            $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');

            /**
            下面这段代码需要修改成直接获取dbAdapter的代码，具体查看
            http://jianxuan.li/2015/03/10/zf2-db-query-result-to-array/
            中的 "prepare for test"
            */
            $dbService = $this->getServiceLocator()->get('Db');
            $adapter = $dbService->getZendDb();
            $sql = new Sql\Sql($adapter);
            $select = $sql->select();

            $select->from(['t' => new Sql\TableIdentifier('se_temperature','public')]);
            $select->columns([
                '*',
                'observe_year' => new Sql\Expression("date_part('year',ts_observe)"),
                'observe_month' => new Sql\Expression("date_part('month',ts_observe)"),
                'observe_day' => new Sql\Expression("date_part('day',ts_observe)"),
                'observe_hour' => new Sql\Expression("date_part('hour',ts_observe)"),
                'observe_minute' => new Sql\Expression("date_part('minute',ts_observe)")
            ]);

            $select->where(['client' => 'edferafe9-34ab-40re-axer-0cdfaer2aad4e']);
            $select->where(function(Sql\Where $where){
                $where->between('ts_observe',date("Y-m-d")." 00:00:00",date("Y-m-d")." 23:59:59");
            });

            $select->order("t.ts_observe ASC");

            /**
            这段代码需要替换成获取Array result的部分，具体查看
            http://jianxuan.li/2015/03/10/zf2-db-query-result-to-array/
            */
            $selectHandle = $dbService->getSelectHandle();
            //$this->ViewModel->setVariable('temperature',$selectHandle->ResultSetToArray($select));

            $response->setContent(json_encode($selectHandle->ResultSetToArray($select), JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK));
            return $response;
        }

        return $this->ViewModel;
    }

}