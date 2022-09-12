<?php
namespace Anyhow\BestSeller\Model;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Sales\Model\ResourceModel\Report\Bestsellers\CollectionFactory as BestSellersCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class PostManagement
{
	protected $bestSellersCollectionFactory;
	protected $productCollectionFactory;
	protected $storeManager;

	public function __construct(
			CollectionFactory $productCollectionFactory,
			StoreManagerInterface $storeManager,
			BestSellersCollectionFactory $bestSellersCollectionFactory
	)
	 {
			$this->bestSellersCollectionFactory = $bestSellersCollectionFactory;
			$this->storeManager = $storeManager;
			$this->productCollectionFactory = $productCollectionFactory;
		}
	/**
	 * {@inheritdoc}
	 */
	public function getPost()
	{
		$collection = $this->getProductCollection();

		if($collection) {
			$data = array(["data"=> $collection->getData()]);
			return json_encode($data);

		}
		$data = array("data"=>"no bestseller products");
		return json_encode($data);
		}

  public function getProductCollection()
  {
      $productIds = [];
      $bestSellers = $this->bestSellersCollectionFactory->create()
          ->setPeriod('year');
      foreach ($bestSellers as $product) {
          $productIds[] = $product->getProductId();
      }
      $collection = $this->productCollectionFactory->create()->addIdFilter($productIds);
      $collection->addMinimalPrice()
          ->addFinalPrice()
          ->addTaxPercents()
          ->addAttributeToSelect('*')
          ->addStoreFilter($this->getStoreId())
          ->setPageSize(count($productIds));
      return $collection;
  }

	public function getStoreId(){
			return $this->storeManager->getStore()->getId();
	}

}
