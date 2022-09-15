<?php

namespace Anyhow\BestSeller\Model;

use Magento\Store\Model\StoreManagerInterface;

class PostManagement
{

	protected $storeManager;
	public function __construct(
			StoreManagerInterface $storeManager,
			\Magento\Framework\App\ResourceConnection $resourceConnection
	)
	 {
			$this->storeManager = $storeManager;
			$this->resourceConnection = $resourceConnection;
		}
	/**
	 * {@inheritdoc}
	 */
	public function getPost()
	{
			$connection = $this->resourceConnection->getConnection();
			$tableName = $this->resourceConnection->getTableName('sales_bestsellers_aggregated_yearly');

			$selectBest = $connection->select()
			 ->from(
			 ['bs' => $tableName])
			  ->order('bs.qty_ordered DESC')
				->where('bs.store_id =?', $this->getStoreId())
	  		->limit(5, 0);

			$dataBest = $connection->fetchAll($selectBest);

			$selectLeast = $connection->select()
			 ->from(
			 ['bs' => $tableName])
			  ->order('bs.qty_ordered ASC')
				->where('bs.store_id =?', $this->getStoreId())
	  		->limit(5, 0);

			$dataLeast = $connection->fetchAll($selectLeast);

			if($dataLeast) {
				$data = ["best-sold"=> $dataBest,
									"least-sold" => $dataLeast
								];
				return json_encode($data);
			}
			$data = ["data"=>"no bestseller products"];
			return json_encode($data);
	}

	public function getStoreId(){
			return $this->storeManager->getStore()->getId();
	}
}

}
