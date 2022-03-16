<?php

namespace TestTaskA1;

class NumericRanges
{
	public $pdo;
	
    public function __construct($pdo)
	{
        $this->pdo = $pdo;
    }
	
	function getNumberColor($inputNumber)
	{
		$item = [];
		
		try {
			$sql = "SELECT * FROM color_ranges WHERE rangeFrom <= :inputNumber AND rangeTo >= :inputNumber";
			
			$statement = $this->pdo->prepare($sql);
			$statement->bindParam(':inputNumber', $inputNumber);
			$statement->execute();
			
		} catch(\PDOException $e) {
			$result = $e->getMessage();
		}
		
		if( $statement->rowCount() > 0 ) {
			$item = $statement->fetch(\PDO::FETCH_ASSOC);
		}
		
		return $item;
	}
	
    public function getAllItems()
	{
		$items = [];
		
		try {
			$sql = "SELECT * FROM color_ranges";
			$statement = $this->pdo->prepare($sql);
			$statement->execute();
			
		} catch(\PDOException $e) {
			$result = $e->getMessage();
		}
		
        if( $statement->rowCount() > 0 ) {
			$items = $statement->fetchAll(\PDO::FETCH_ASSOC);
        }
		
        return $items;
    }
	
	
	public function updateItem($data)
	{
		$result = $this->isRangeOverlapping($data);
		
		if ($result === true) {
			$updateData = [
				'color' => $data['color'],
				'rangeFrom' => $data['rangeFrom'],
				'rangeTo' => $data['rangeTo'],
				'id' => $data['id']
			];
			
			$sql = "UPDATE color_ranges SET color=:color, rangeFrom=:rangeFrom, rangeTo=:rangeTo WHERE id=:id";
			try {
				$statement = $this->pdo->prepare($sql);
				$result = $statement->execute($updateData);
				
			} catch(\PDOException $e) {
				$result = $e->getMessage();
			}	
		}
		
		return $result;
	}
	
	
	public function isRangeOverlapping($data)
	{
		$items = [];
		$result = true;
		
		try {
			$sql = "SELECT * FROM color_ranges WHERE id NOT IN(:id)";
			
			$statement = $this->pdo->prepare($sql);
			$statement->bindParam(':id', $data['id']);
			$statement->execute();
			
		} catch(\PDOException $e){
			$result = $e->getMessage();
		}
		
		if( $statement->rowCount() > 0 )
		{
			$items = $statement->fetchAll(\PDO::FETCH_ASSOC);
		
			foreach($items as $item) {		
				if((( $data['rangeFrom'] <= $item['rangeTo']) && ($data['rangeFrom'] >= $item['rangeFrom'] )) ||
					(( $data['rangeTo'] <= $item['rangeTo']) && ($data['rangeTo'] >= $item['rangeFrom'] ))) {
						return false;
				}
			}
		}
		
		return $result;
	}
}