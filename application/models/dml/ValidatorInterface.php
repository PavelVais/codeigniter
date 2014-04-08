<?php
namespace DML;
/**
 * @name ValidatorInterface
 * @version 1.2 
 * @changelog
 *  - Tento validator byl pridan do namespace DML
 *  - insert validator jiz neocheckuje nulove hodnoty pri nulovych sloupcich
 * @author Pavel Vais
 */
interface ValidatorInterface
{

	public function set_data(Table $table_info, $data);

	public function validate();
}

class ValidatorInsert extends Validator implements ValidatorInterface
{

	public function set_data(Table $table_info, $data)
	{
		$this->table_info = $table_info;
		$this->data = $data;
	}

	/**
	 * Zkontrolovani vsech vstupnich udaju (vlozenych pres 
	 * fetch_data, popr. add_data) s udaji z tabulky
	 * @return boolean
	 * @throws DBException
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
					throw new DBException( DBException::ERROR_COL_LENGTH, DBException::ERROR_NUMBER_COL_LENGTH,array($data_value, $column_inf_value['length']) );

				if ( $check && $this->check_data_type( $column_inf_value['type'], $data_value ) == FALSE )
				{
					if ( $column_inf_value['type'] == Table::COL_TYPE_INT && $key != null  )
						throw new DBException( DBException::ERROR_NOT_NUMBER, DBException::ERROR_NUMBER_NOT_NUMBER, $data_value );
					elseif ( $column_inf_value['type'] == Table::COL_TYPE_DATE || $column_inf_value['type'] == Table::COL_TYPE_DATETIME )
						throw new DBException( DBException::ERROR_NOT_DATE,DBException::ERROR_NUMBER_NOT_DATE, $data_value );
				}
				
				unset($data[$data_key]);
			}

			if ( !$check && $column_inf_value['is_nullable'] == false && $column_inf_value['is_primary'] == false)
			{
				throw new DBException( DBException::ERROR_NOT_NULL,DBException::ERROR_NUMBER_NOT_NULL, $key );
			}
		}
		return TRUE;
	}

}

class ValidatorUpdate extends Validator implements ValidatorInterface
{

	public function set_data(Table $table_info, $data)
	{
		$this->table_info = $table_info;
		$this->data = $data;
	}

	/**
	 * Validace dle UPDATU: pri validovani se ignoruje is_null hodnota
	 * @return boolean
	 * @throws DBException 
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
					throw new DBException( DBException::ERROR_NOT_NULL,DBException::ERROR_NUMBER_NOT_NULL, $key );
				}
				
				if ( $column_inf_value['length'] > 0 && strlen(utf8_decode($data_value)) > $column_inf_value['length'] )
					throw new DBException( DBException::ERROR_COL_LENGTH, DBException::ERROR_NUMBER_COL_LENGTH,array($data_value, $column_inf_value['length']) );

				if ( $this->check_data_type( $column_inf_value['type'], $data_value ) == FALSE )
				{
					if ( $column_inf_value['type'] == Table::COL_TYPE_INT  && $data_value != null)
						throw new DBException( DBException::ERROR_NOT_NUMBER, DBException::ERROR_NUMBER_NOT_NUMBER, $data_value );
					elseif ( $column_inf_value['type'] == Table::COL_TYPE_DATE || $column_inf_value['type'] == Table::COL_TYPE_DATETIME )
						throw new DBException( DBException::ERROR_NOT_DATE,DBException::ERROR_NUMBER_NOT_DATE, $data_value );
				}
				
				unset($data[$data_key]);
			}
		}
		return TRUE;
	}

}