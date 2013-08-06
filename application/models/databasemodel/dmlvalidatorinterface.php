<?php

/**
 * @name DMLValidatorInterface
 * @version 1.1 
 * @changelog
 *  - insert validator jiz neocheckuje nulove hodnoty pri nulovych sloupcich
 * @author Pavel Vais
 */
interface DMLValidatorInterface
{

	public function set_data(DMLTable $table_info, $data);

	public function validate();
}

class DMLValidatorInsert extends DMLValidator implements DMLValidatorInterface
{

	public function set_data(DMLTable $table_info, $data)
	{
		$this->table_info = $table_info;
		$this->data = $data;
	}

	/**
	 * Zkontrolovani vsech vstupnich udaju (vlozenych pres 
	 * fetch_data, popr. add_data) s udaji z tabulky
	 * @return boolean
	 * @throws DMLException
	 */
	public function validate()
	{
		$columns = $this->table_info->get_columns();
		$data = $this->data;
		foreach ( $columns as $key => $column_inf_value )
		{
			
			$check = false;
			foreach ( $data as $data_key => $data_value )
			{
				if ($data_key != $key) continue;
				
				if ( $column_inf_value['is_nullable'] == false && strlen($data_value) > 0 )
				{
					$check = true;
				}

				if ( $column_inf_value['length'] > 0 && strlen(utf8_decode($data_value)) > $column_inf_value['length'] )
					throw new DMLException( DMLException::ERROR_COL_LENGTH, DMLException::ERROR_NUMBER_COL_LENGTH,array($data_value, $column_inf_value['length']) );

				if ( $check && $this->check_data_type( $column_inf_value['type'], $data_value ) == FALSE )
				{
					if ( $column_inf_value['type'] == DMLTable::COL_TYPE_INT && $key != null  )
						throw new DMLException( DMLException::ERROR_NOT_NUMBER, DMLException::ERROR_NUMBER_NOT_NUMBER, $data_value );
					elseif ( $column_inf_value['type'] == DMLTable::COL_TYPE_DATE || $column_inf_value['type'] == DMLTable::COL_TYPE_DATETIME )
						throw new DMLException( DMLException::ERROR_NOT_DATE,DMLException::ERROR_NUMBER_NOT_DATE, $data_value );
				}
				
				unset($data[$data_key]);
			}

			if ( !$check && $column_inf_value['is_nullable'] == false && $column_inf_value['is_primary'] == false)
			{
				throw new DMLException( DMLException::ERROR_NOT_NULL,DMLException::ERROR_NUMBER_NOT_NULL, $key );
			}
		}
		return TRUE;
	}

}

class DMLValidatorUpdate extends DMLValidator implements DMLValidatorInterface
{

	public function set_data(DMLTable $table_info, $data)
	{
		$this->table_info = $table_info;
		$this->data = $data;
	}

	/**
	 * Validace dle UPDATU: pri validovani se ignoruje is_null hodnota
	 * @return boolean
	 * @throws DMLException 
	 */
	public function validate()
	{

		$columns = $this->table_info->get_columns();
		$data = $this->data;
		foreach ( $columns as $key => $column_inf_value )
		{
			
			foreach ( $data as $data_key => $data_value )
			{
				if ($data_key != $key) continue;
				
				if ( $column_inf_value['is_nullable'] == false && $data_value === "" )
				{
					throw new DMLException( DMLException::ERROR_NOT_NULL,DMLException::ERROR_NUMBER_NOT_NULL, $key );
				}
				
				if ( $column_inf_value['length'] > 0 && strlen(utf8_decode($data_value)) > $column_inf_value['length'] )
					throw new DMLException( DMLException::ERROR_COL_LENGTH, DMLException::ERROR_NUMBER_COL_LENGTH,array($data_value, $column_inf_value['length']) );

				if ( $this->check_data_type( $column_inf_value['type'], $data_value ) == FALSE )
				{
					if ( $column_inf_value['type'] == DMLTable::COL_TYPE_INT  && $data_value != null)
						throw new DMLException( DMLException::ERROR_NOT_NUMBER, DMLException::ERROR_NUMBER_NOT_NUMBER, $data_value );
					elseif ( $column_inf_value['type'] == DMLTable::COL_TYPE_DATE || $column_inf_value['type'] == DMLTable::COL_TYPE_DATETIME )
						throw new DMLException( DMLException::ERROR_NOT_DATE,DMLException::ERROR_NUMBER_NOT_DATE, $data_value );
				}
				
				unset($data[$data_key]);
			}
		}
		return TRUE;
	}

}