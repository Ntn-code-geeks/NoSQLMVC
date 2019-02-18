<?php
mysqli_report(MYSQLI_REPORT_ALL ^ MYSQLI_REPORT_INDEX);

class db_class
{
    private $db_connection_type;

    private $db_error_code;
    private $db_error_message;

    private $db_link;
    private $db_link_read;

    private $db_host_ip;
    private $db_user_name;
    private $db_password;
    private $db_database_name;

    private $db_host_ip_read;
    private $db_user_name_read;
    private $db_password_read;
    private $db_database_name_read;

    private $db_server_type;
    private $db_charset;
    private $db_port;
    private $db_socket;

    private $db_server_type_read;
    private $db_charset_read;
    private $db_port_read;
    private $db_socket_read;

    private $db_query;
    private $db_last_query;
    private $db_count = 0;
    private $db_result = array();

    private $db_bind_params = array();

    private $db_join;
    private $db_joinAnd = array();

    private $db_table_name;

    private $db_last_insert_id;

    private $db_where = array();

    private $db_column = array();
    private $db_group_by = array();
    private $db_order_by = array();

    private $db_transaction_in_progress = false;

    private $db_output_procedure;

    private $db_export_file_path;
    private $db_export_file_settings;

    public function __construct($fn = null, $connection_type = null)
    {
        if ($connection_type !== null) {
            $this->db_connection_type = $connection_type;

        } else {
            $this->db_connection_type = 'mySQLi';
        }
        if ($fn !== null) {
            $this->$fn();
        }
    }

    public final function getError()
    {
        return $this->db_error_message . ' (' . $this->db_error_code . ')';
    }

    public final function getErrorCode()
    {
        return $this->db_error_code;
    }

    public final function getLastQuery()
    {
        return $this->db_last_query;
    }

    public final function getLastInsertID()
    {
        return $this->db_last_insert_id;
    }

    public final function getCount()
    {
        return $this->db_count;
    }


    public final function isConnected()
    {
        if (is_object($this->db_link))
            return true;
        else {
            $this->db_error_message = "No Connection is open/build";
            $this->db_error_code = '';
            return false;
        }
    }

// Connection Database Function
    public final function connect($host_ip, $user_name, $password, $database_name, $db_server_type = null, $charset = null, $port = null, $socket = null){

        $this->reset_parameters_initial();
        $this->db_link = null;
        $this->db_link_read = null;

        if ($host_ip == null || $database_name == null || $user_name == null || $password == null) {
            $this->db_error_message = 'Provided all required values for connection';
            return false;
        } else {
            $this->db_host_ip = $host_ip;
            $this->db_user_name = $user_name;
            $this->db_password = $password;
            $this->db_database_name = $database_name;
            $this->db_server_type = $db_server_type;
            $this->db_charset = $charset;
            $this->db_port = $port;
            $this->db_socket = $socket;
            return $this->connectDB();
        }
    }



    private function connectDB()
    {
        try {
            if ($this->db_connection_type === 'mySQLi') {
                if ($this->db_host_ip == null || $this->db_user_name == null || $this->db_password == null || $this->db_database_name == null) {
                    $this->db_error_message = 'Connection Values not Provided';
                    return false;
                } else {
                    if ($this->db_charset == null)
                        $this->db_charset = 'utf-8';
                    $this->db_link = new mysqli($this->db_host_ip, $this->db_user_name, $this->db_password, $this->db_database_name, $this->db_port, $this->db_socket);
                    if ($this->db_host_ip_read != null && $this->db_user_name_read != null && $this->db_password_read != null && $this->db_database_name_read != null) {
                        if ($this->db_charset_read == null)
                            $this->db_charset_read = 'utf-8';
                        $this->db_link_read = new mysqli($this->db_host_ip_read, $this->db_user_name_read, $this->db_password_read, $this->db_database_name_read, $this->db_port_read, $this->db_socket_read);
                    }
                    return true;
                }
            } elseif ($this->db_connection_type === 'PDO') {
                if ($this->db_host_ip == null || $this->db_user_name == null || $this->db_password == null || $this->db_database_name == null) {
                    $this->db_error_message = 'Connection Values not Provided';
                    return false;
                } else {
                    $initial = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_AUTOCOMMIT => 1);
                    $this->db_link = new PDO('mysql:dbname=' . $this->db_database_name . ';host=' . $this->db_host_ip . ';charset=UTF8', $this->db_user_name, $this->db_password, $initial);
                    if ($this->db_host_ip_read != null && $this->db_user_name_read != null && $this->db_password_read != null && $this->db_database_name_read == null)
                        $this->db_link = new PDO('mysql:dbname=' . $this->db_database_name_read . ';host=' . $this->db_host_ip_read . ';charset=UTF8', $this->db_user_name_read, $this->db_password_read, $initial);
                    return true;
                    // $this->db_link->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
                }
            } else {
                $this->db_error_message = 'Invalid db connection type';
                return false;
            }
        } catch (Exception $e) {
            $this->db_error_message = "Connection failed " . $e->getMessage();
            $this->db_error_code = $e->getCode();
            return false;
        }
    }
	
	
	
	

    public function insert($table_name, $insert_data) //final date 24May2017
    {
        $this->reset_parameters_initial();
        if (empty($table_name)) {
            $this->db_error_message = 'TableName not provided';
            $this->db_error_code = '';
            return false;
        } elseif (empty($insert_data) || !is_array($insert_data)) {
            $this->db_error_message = 'Insert Data not provided';
            $this->db_error_code = '';
            return false;
        } else {
            if (!$this->isConnected())
                return false;
            else {
                $this->db_query = 'INSERT INTO ' . $table_name;
                $res = $this->build_query(null, $insert_data);
                if ($res)
                    return $this->db_count;
                else
                    return false;
            }
        }
    }

    public final function insertMulti($table_name, $multi_insert_data) //final date 24May2017
    {
        return $this->insert($table_name, $multi_insert_data);
    }

    public function delete($table_name, $where, $limit = null) //final date 24May2017
    {
        $this->reset_parameters_initial();
        if (empty($table_name)) {
            $this->db_error_message = 'TableName not provided';
            $this->db_error_code = '';
            return false;
        } elseif (empty($where) || !is_array($where)) {
            $this->db_error_message = 'Where Data not provided for Delete';
            $this->db_error_code = '';
            return false;
        } else {
            if (!$this->isConnected())
                return false;
            else {
                $this->where_recursive($where);
                // $this->where_built($where);
                $this->db_query = "DELETE FROM " . $table_name;
                $res = $this->build_query($limit);
                if ($res)
                    return $this->db_count;
                else
                    return false;
            }
        }
    }

    public function update($table_name, $update_data, $where = array(), $order_by = array(), $limit = null) //final date 24May2017
    {
        $this->reset_parameters_initial();
        if (empty($table_name)) {
            $this->db_error_message = 'TableName not provided';
            $this->db_error_code = '';
            return false;
        } elseif (empty($update_data) || !is_array($update_data)) {
            $this->db_error_message = 'Update Data not provided';
            $this->db_error_code = '';
            return false;
        } elseif ((empty($where) || !is_array($update_data)) && (empty($this->db_where) || !is_array($this->db_where))) {
            $this->db_error_message = 'Where Data not provided';
            $this->db_error_code = '';
            return false;
        } else {
            if (!$this->isConnected())
                return false;
            else {
                if (!empty($where))
                    //  $this->where_built($where);
                    $this->where_recursive($where);
                if (!empty($order_by)) {
                    if (!$this->orderBy_build($order_by))
                        return false;
                }

                $this->db_query = "UPDATE " . $table_name;
                $res = $this->build_query($limit, $update_data);
                if ($res)
                    return $this->db_count;
                else
                    return false;
            }
        }
    }

    public function get($table_name, $where = array(), $columns = '*', $order_by = array(), $group_by = array(), $limit = null)
    {
        $this->reset_parameters_initial();
        if (empty($table_name)) {
            $this->db_error_message = 'TableName not provided';
            $this->db_error_code = '';
            return false;
        } else {
            if (!$this->isConnected())
                return false;
            else {
                if (empty($columns)) {
                    $columns = '*';
                }

//                if (!empty($where))
//                    $this->where_built($where);
                if (!empty($where)) {
                    $this->where_recursive($where);
                }
                if (!empty($group_by)) {
                    $this->GroupBy($group_by);
                }
                if (!empty($order_by)) {
                    if (!$this->orderBy_build($order_by))
                        return false;
                }
                $column = is_array($columns) ? implode(', ', $columns) : $columns;
                $this->db_table_name = $table_name;
                $this->db_query = 'SELECT ' . implode(' ', $this->db_column) . ' ' . $column . " FROM " . $this->db_table_name;
                $res = $this->build_query($limit);
                if ($res)
                    return $this->db_result;
                else
                    return false;
            }
        }
    }

    public function getOne($table_name, $where = array(), $columns = '*')
    {
        $reposne = $this->get($table_name, $where, $columns, '', '', '1');
        return $reposne[0];
    }

    public function getMulti($table_name, $where = array(), $columns = '*', $order_by = array())
    {
        $this->reset_parameters_initial();
        if (empty($table_name) || !is_array($table_name)) {
            $this->db_error_message = 'TableName data not provided';
            $this->db_error_code = '';
            return false;
        } else {
            $result = array();
            foreach ($table_name as $val) {
                $reposne = $this->get($val, $where, $columns, $order_by);
                if ($reposne) {
                    $result = array_merge($result, $reposne);
                } else
                    return $reposne;
            }
            return $result;
        }
    }

    public final function startTxn()//goFinal
    {
        if (!$this->isConnected())
            return false;
        else {
            if ($this->db_transaction_in_progress) {
                $this->db_error_message = 'Transaction Already InProcess';
                $this->db_error_code = '';
                return false;
            } else {
                try {
                    if ($this->db_connection_type === 'mySQLi')
                        $result = $this->db_link->autocommit(false);
                    else
                        $result = $this->db_link->beginTransaction();
                    $this->db_transaction_in_progress = true;
                    register_shutdown_function(array($this, "_transaction_status_check"));
                    return $result;
                } catch (Exception $e) {
                    $this->db_error_message = "Start transaction failed - " . $e->getMessage();
                    $this->db_error_code = $e->getCode();
                    return false;
                } 
				finally {
                    $this->reset_parameters();
                }
            }
        }
    }

    public final function commitTxn()//goFinal
    {
        if (!$this->isConnected())
            return false;
        else {
            if (!$this->db_transaction_in_progress) {
                $this->db_error_message = 'Not in Transaction';
                $this->db_error_code = '';
                return false;
            } else {
                try {
                    $result = $this->db_link->commit();
                    $this->db_transaction_in_progress = false;
                    if ($this->db_connection_type === 'mySQLi')
                        $this->db_link->autocommit(true);
                    else
                        $this->db_link->setAttribute(PDO::ATTR_AUTOCOMMIT, 1);
                    return $result;
                } catch (Exception $e) {
                    $this->db_error_message = "Connection failed " . $e->getMessage();
                    $this->db_error_code = $e->getCode();
                    return false;
                } finally {
                    $this->reset_parameters();
                }
            }
        }
    }

    public final function rollbackTxn() //goFinal
    {
        if (!$this->isConnected())
            return false;
        else {
            if (!$this->db_transaction_in_progress) {
                $this->db_error_message = 'Not in Transaction';
                $this->db_error_code = '';
                return false;
            } else {
                try {
                    if ($this->db_connection_type === 'mySQLi') {
                        $result = $this->db_link->rollback();
                        $this->db_link->autocommit(true);
                    } else {
                        $result = $this->db_link->rollBack();
                        $this->db_link->setAttribute(PDO::ATTR_AUTOCOMMIT, 1);
                    }
                    $this->db_transaction_in_progress = false;
                    return $result;
                } catch (Exception $e) {
                    $this->db_error_message = "Connection failed " . $e->getMessage();
                    $this->db_error_code = $e->getCode();
                    return false;
                } finally {
                    $this->reset_parameters();
                }
            }
        }
    }

    public function setQueryOption($options)
    {
        $allowedOptions = Array('DISTINCT', 'DISTINCTROW', 'HIGH_PRIORITY', 'STRAIGHT_JOIN', 'SQL_SMALL_RESULT',
            'SQL_BIG_RESULT', 'SQL_BUFFER_RESULT', 'SQL_CACHE', 'SQL_NO_CACHE', 'SQL_CALC_FOUND_ROWS',
            'LOW_PRIORITY', 'IGNORE', 'QUICK', 'FOR UPDATE', 'LOCK IN SHARE MODE');

        if (!is_array($options)) {
            $options = Array($options);
        }

        foreach ($options as $option) {
            $option = strtoupper($option);
            if (!in_array($option, $allowedOptions)) {
                throw new Exception('Wrong query option: ' . $option);
            } elseif ($option == 'FOR UPDATE') {
                $this->_forUpdate = true;
            } elseif ($option == 'LOCK IN SHARE MODE') {
                $this->_lockInShareMode = true;
            } else {
                $this->db_column[] = $option;
            }
        }

        return $this;
    }

    public function not($col = null)
    {
        return array("[N]" => (string)$col);
    }

    public function inc($num = 1)
    {
        if (!is_numeric($num)) {
            throw new Exception('Argument supplied to inc must be a number');
        }
        return array("[I]" => "+" . $num);
    }

    public final function _transaction_status_check() //goFinal
    {
        if (!$this->db_transaction_in_progress) {
            return;
        }
        $this->rollbackTxn();
    }

    public final function groupBy($groupByField)//goFinal
    {
        if (is_array($groupByField)) {
            foreach ($groupByField as $val) {


                $val = preg_replace("/[^-a-z0-9\.\(\),_\*]+/i", '', $val);
                $this->db_group_by[] = $val;
            }
        } else {
            $groupByField = preg_replace("/[^-a-z0-9\.\(\),_\*]+/i", '', $groupByField);
            $this->db_group_by[] = $groupByField;
        }
        return true;
    }

    public function orderBy($orderByField, $orderbyDirection = "DESC", $customFields = null)
    {
        $orderbyDirection = strtoupper(trim($orderbyDirection));
        if (!in_array($orderbyDirection, array("ASC", "DESC"))) {
            $this->db_error_message = 'OrderBy can be ASC or DESC : ' . $orderbyDirection;
            $this->db_error_code = '';
            return false;
        } else {
            $orderByField = preg_replace("/[^-a-z0-9\.\(\),_`\*\'\"]+/i", '', $orderByField);
            if (is_array($customFields)) {
                foreach ($customFields as $key => $value) {
                    $customFields[$key] = preg_replace("/[^-a-z0-9\.\(\),_` ]+/i", '', $value);
                }
                $orderByField = 'FIELD (' . $orderByField . ', "' . implode('","', $customFields) . '")';
            }
            $this->db_order_by[$orderByField] = $orderbyDirection;
            return true;
        }
    }

    private final function orderBy_build($order_by)
    {
        if (is_array($order_by)) {
            foreach ($order_by as $val) {
                if (is_array($val)) {
                    if (!$this->orderBy($val[0], isset($val[1]) ? $val[1] : null, isset($val[2]) ? $val[2] : null))
                        return false;
                } else {
                    if (!$this->orderBy($val))
                        return false;
                }
            }
        } else {
            if (!$this->orderBy($order_by))
                return false;
        }
        return true;
    }

    public final function join($joinTable, $joinCondition, $joinType = '')//goFinal
    {
        $allowedTypes = array('LEFT', 'RIGHT', 'OUTER', 'INNER', 'LEFT OUTER', 'RIGHT OUTER');
        $joinType = strtoupper(trim($joinType));

        if ($joinType && !in_array($joinType, $allowedTypes)) {
            $this->db_error_message = 'Wrong JOIN type : ' . $joinType;
            $this->db_error_code = '';
            return false;
        }

        $this->db_join[] = array($joinType, $joinTable, $joinCondition);

        return true;
    }

    public final function joinWhere($whereJoin, $whereProp, $whereValue = 'DBNULL', $operator = '=', $cond = 'AND')//goFinal
    {
        $this->db_joinAnd[$whereJoin][] = array($cond, $whereProp, $operator, $whereValue);
        return true;
    }

    public function where($whereProp, $whereValue = 'DBNULL', $operator = '=', $cond = 'AND')
    {
        if (count($this->db_where) == 0) {
            $cond = '';
        }
        $this->db_where[] = array($cond, $whereProp, $operator, $whereValue);
        return true;
    }

    public function orWhere($whereProp, $whereValue = 'DBNULL', $operator = '=')
    {
        return $this->where($whereProp, $whereValue, $operator, 'OR');
    }

    private function where_or($where)
    {

        if (!empty($where[1]) && is_array($where[1])) {
            foreach ($where as $val) {
                $this->orWhere($val[0], $val[1], isset($val[2]) ? $val[2] : '=', isset($val[3]) ? $val[3] : 'OR');
            }
        } else
            $this->orWhere($where[0], $where[1], isset($where[2]) ? $where[2] : '=', isset($where[3]) ? $where[3] : 'OR');
        return true;
    }

    private function where_built($where)
    {
        if (!empty($where[0]) && is_array($where[0])) {
            foreach ($where as $val) {
                $this->where($val[0], $val[1], isset($val[2]) ? $val[2] : '=', isset($val[3]) ? $val[3] : 'AND');
            }
        } else
            $this->where($where[0], $where[1], isset($where[2]) ? $where[2] : '=', isset($where[3]) ? $where[3] : 'AND');
        return true;
    }

    private function where_recursive($where)
    {
        $store_array = array();
        foreach ($where as $key => $val) {
            $store_array[] = $val;
            if (!is_array($val)) {
                if ($key == 0 && $val[0] == 'OR' || $val[0] == 'AND') {
                    $this->db_error_message = 'Wrong operations';
                    throw new Exception('Check Mannual For Performing This Type of Conditions');
                    break;
                } else
                    return $this->where_built($where);
            }
            if (is_array($val) || $val[0] == 'AND' || $val[0] == 'OR') {
                if ($val[0] == 'AND') {
                    unset($store_array[$key]);
                    $this->where_built($store_array);
                    $where = array_slice($where, $key + 1);
                    return $this->where_recursive($where);
                }
                if ($val[0] == 'OR') {
                    unset($store_array[$key]);
                    $this->where_or($store_array);
                    $where = array_slice($where, $key + 1);
                    return $this->where_recursive($where);
                }
            }
        }
        if (!empty($where))
            return $this->where_built($store_array);
    }

    public final function selectDB($db_name)
    {
        if (!$this->isConnected())
            return false;
        else {
            try {
                /*if($db_name == $this->db_database_name){

                }
                    return true;*/

                if ($this->db_connection_type === 'mySQLi') {
                    if ($this->db_link->select_db($db_name)) {
                        if (is_object($this->db_link_read)) {
                            if ($this->db_link_read->select_db($db_name)) {
                                $this->db_database_name = $this->db_database_name_read = $db_name;
                                return true;
                            } else {
                                if ($this->db_link->select_db($this->db_database_name))
                                    return false;
                                else
                                    $this->db_link = null;
                                return false;
                            }
                        } else {
                            $this->db_database_name = $db_name;
                            return true;
                        }
                    } else
                        return false;
                }
                if ($this->db_connection_type === 'PDO') {
                    $this->db_database_name = $db_name;
                    $this->db_link = '';
                    return $this->connect($this->db_host_ip, $this->db_user_name, $this->db_password, $db_name, $this->db_server_type, $this->db_charset, $this->db_port, $this->db_socket);
                }
            } catch (Exception $e) {
                $this->db_error_message = "Select DB failed - " . $e->getMessage();
                $this->db_error_code = $e->getCode();
                return false;
            } finally {
                $this->reset_parameters();
            }
        }
    }

    public final function callProcedure($procedure_name, $call_data)
    {
        $this->reset_parameters_initial();
        if (empty($procedure_name)) {
            $this->db_error_message = 'Procedure Name not provided';
            $this->db_error_code = '';
            return false;
        } elseif (empty($call_data)) {
            $this->db_error_message = 'Procedure Data not provided';
            $this->db_error_code = '';
            return false;
        } else {
            if (!$this->isConnected())
                return false;
            else {
                $this->db_query = 'CALL `' . $procedure_name . '` ';
                if (!is_array($call_data))
                    $call_data = array('1' => $call_data);
                $res = $this->build_query(null, $call_data);
                if ($res)
                    return $this->db_result;
                else
                    return false;
            }
        }
    }

    public function importData($table_name, $path_with_file, $settings = null)
    {
        $this->reset_parameters_initial();
        if (empty($table_name)) {
            $this->db_error_message = 'Table Name not provided';
            $this->db_error_code = '';
            return false;
        } elseif (!file_exists($path_with_file)) {
            $this->db_error_message = 'File not exist';
            $this->db_error_code = '';
            return false;
        } else {
            try {
                $i_settings = array("fieldChar" => ',', "lineChar" => PHP_EOL, "linesToIgnore" => 1, "option" => '\'""\'');
                if (gettype($settings) == "array")
                    $i_settings = array_merge($i_settings, $settings);

                $path_with_file = str_replace("\\", "\\\\", $path_with_file);

                $this->db_query = sprintf('LOAD DATA INFILE \'%s\'IGNORE INTO TABLE %s', $path_with_file, $table_name);
                $this->db_query .= sprintf('CHARACTER SET UTF8 ');
                $this->db_query .= sprintf(' FIELDS TERMINATED BY \'%s\'', $i_settings["fieldChar"]);
                if (isset($i_settings["fieldEnclosure"]))
                    $this->db_query .= sprintf(' ENCLOSED BY \'%s\'', $i_settings["fieldEnclosure"]);

                $this->db_query .= sprintf(' OPTIONALLY ENCLOSED BY  \'"\'');

                $this->db_query .= sprintf(' LINES TERMINATED BY \'%s\'', $i_settings["lineChar"]);
                if (isset($i_settings["lineStarting"]))
                    $this->db_query .= sprintf(' STARTING BY \'%s\'', $i_settings["lineStarting"]);

                $this->db_query .= sprintf(' IGNORE %d LINES', $i_settings["linesToIgnore"]);

                $this->db_last_query = $this->db_query;
                return $this->db_link->query($this->db_query);
            } catch (Exception $e) {
                $this->db_error_message = "Can't Import Data - " . $e->getMessage();
                $this->db_error_code = $e->getCode();
                return false;
            } finally {
                $this->reset_parameters();
            }
        }
    }

    public final function exportData($path_with_file, $settings = null, $table_name, $where = array(), $columns = '*', $order_by = array(), $limit = null)
    {
        $this->reset_parameters_initial();
        if (empty($path_with_file)) {
            $this->db_error_message = 'File Path not provided';
            $this->db_error_code = '';
            return false;
        } elseif (file_exists($path_with_file)) {
            $this->db_error_message = 'File already exist';
            $this->db_error_code = '';
            return false;
        } else {
            $this->db_export_file_path = $path_with_file;
            $this->db_export_file_settings = $settings;
            $this->get($table_name, $where, $columns, $order_by, $limit);
            if (file_exists($path_with_file))
                return true;
            else
                return false;
        }
    }

    public function now($diff = null, $func = "NOW()")
    {
        return array("[F]" => array($this->interval($diff, $func)));
    }

    public function interval($diff, $func = "NOW()")
    {
        $types = array("s" => "second", "m" => "minute", "h" => "hour", "d" => "day", "M" => "month", "Y" => "year");
        $incr = '+';
        $items = '';
        $type = 'd';

        if ($diff && preg_match('/([+-]?) ?([0-9]+) ?([a-zA-Z]?)/', $diff, $matches)) {
            if (!empty($matches[1])) {
                $incr = $matches[1];
            }

            if (!empty($matches[2])) {
                $items = $matches[2];
            }

            if (!empty($matches[3])) {
                $type = $matches[3];
            }

            if (!in_array($type, array_keys($types))) {
                throw new Exception("invalid interval type in '{$diff}'");
            }

            $func .= " " . $incr . " interval " . $items . " " . $types[$type] . " ";
        }
        return $func;
    }


    private final function build_export_data()
    {
        if (!$this->db_export_file_path) {
            return;
        }

        $settings = array("fieldChar" => ',', "lineChar" => PHP_EOL, "linesToIgnore" => 1, "option" => '\'""\'', "newline" => '\n');

        if (gettype($this->db_export_file_settings) == "array")
            $settings = array_merge($settings, $this->db_export_file_settings);

        // Add 1 more slash to every slash so maria will interpret it as a path
        $export_file = str_replace("\\", "\\\\", $this->db_export_file_path);

        $this->db_query .= sprintf(' INTO OUTFILE \'%s\'', $export_file);

        // FIELDS
        $this->db_query .= sprintf(' FIELDS TERMINATED BY \'%s\'', $settings["fieldChar"]);
        if (isset($settings["fieldEnclosure"]))
            $this->db_query .= sprintf(' ENCLOSED BY \'%s\'', $settings["fieldEnclosure"]);

        $this->db_query .= sprintf(' ENCLOSED BY  \'"\'');

        // LINES
        $this->db_query .= sprintf(' LINES TERMINATED BY \'%s\'', $settings["newline"]);
        if (isset($settings["lineStarting"]))
            $this->db_query .= sprintf(' STARTING BY \'%s\'', $settings["lineStarting"]);
    }

    private final function ref_values(array &$arr)
    {
        if (strnatcmp(phpversion(), '5.3') >= 0) {
            $refs = array();
            foreach ($arr as $key => $value) {
                $refs[$key] = &$arr[$key];
            }
            return $refs;
        }
        return $arr;
    }

    private final function build_query($numRows = null, $tableData = null) //final
    {
        $this->_buildJoin();
        $this->build_insert_query($tableData);
        $this->_buildCondition('WHERE', $this->db_where);
        $this->_buildGroupBy();
        $this->_buildOrderBy();
        $this->_buildLimit($numRows);
        $this->build_export_data();
        try {

            $this->db_last_query = $this->generate_last_query($this->db_query, $this->db_bind_params);
            $link = $this->db_link;
            if ($this->db_transaction_in_progress === false && is_object($this->db_link_read) && preg_match('/^SELECT/', $this->db_query))
                $link = $this->db_link_read;
            $statement = $link->prepare($this->db_query);
            if (!$statement)
                return false;
//log
            $this->db_count = 0;
            if ($this->db_connection_type === 'mySQLi') {
                if (count($this->db_bind_params) >= 1)
                    call_user_func_array(array($statement, 'bind_param'), $this->ref_values($this->db_bind_params));
                $statement->execute();
                if ($this->db_export_file_path)
                    return true;
                if (preg_match('/^[SELECT|CALL]/', $this->db_query)) {
                    $this->db_result = array();
                    $parameters = array();
                    $shouldStoreResult = false;
                    $meta = $statement->result_metadata();
                    if (!$meta && $statement->sqlstate)
                        return false;
                    $row = array();
                    while ($field = $meta->fetch_field()) {
                        if ($field->type == 252)
                            $shouldStoreResult = true;
                        $row[$field->name] = null;
                        $parameters[] = &$row[$field->name];
                    }
                    if ($shouldStoreResult)
                        $statement->store_result();
                    call_user_func_array(array($statement, 'bind_result'), $parameters);
                    $this->db_count = 0;
                    while ($statement->fetch()) {
                        $result = array();
                        foreach ($row as $key => $val) {
                            if (is_array($val)) {
                                foreach ($val as $k => $v)
                                    $result[$key][$k] = $v;
                            } else
                                $result[$key] = $val;
                        }
                        $this->db_count++;
                        array_push($this->db_result, $result);
                    }
                    if ($shouldStoreResult)
                        $statement->free_result();
                    if (!empty($this->db_output_procedure)) {
                        $in_result = $this->db_result;
                        $this->db_result = array();
                        $this->db_result['in'] = $in_result;
                        $statement->close();
                        $statement = $link->query('Select ' . $this->db_output_procedure);
                        $out_result = $statement->fetch_assoc();
                        foreach ($out_result as $key => $val) {
                            $this->db_count++;
                            $this->db_result['out'][str_replace('@', '', $key)] = $val;
                        }
                    }
                } else {
                    $this->db_count = $statement->affected_rows;
                    if (preg_match('/^[INSERT]/', $this->db_query)) {
                        $this->db_last_insert_id = $link->insert_id;
                    }
                }
                $statement->close();
            } elseif ($this->db_connection_type === 'PDO') {
                foreach ($this->db_bind_params as $key => $val) {
                    $statement->bindValue($key + 1, $val[0], $val[1]);
                }
                $statement->execute();

                if ($this->db_export_file_path)
                    return true;
                if (preg_match('/^[SELECT|CALL]/', $this->db_query)) {
                    $this->db_result = $statement->fetchAll(PDO::FETCH_ASSOC);
                    $this->db_count = $statement->rowCount();
                    if (!empty($this->db_output_procedure)) {
                        $in_result = $this->db_result;
                        $this->db_result = array();
                        $this->db_result['in'] = $in_result;
                        $statement->closeCursor();
                        $statement = $link->query('Select ' . $this->db_output_procedure);
                        $out_result = $statement->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($out_result[0] as $key => $val) {
                            $this->db_count++;
                            $this->db_result['out'][str_replace('@', '', $key)] = $val;
                        }
                    }
                } else {
                    $this->db_count = $statement->rowCount();
                    if (preg_match('/^[INSERT]/', $this->db_query)) {
                        $this->db_last_insert_id = $link->lastInsertId();
                    }
                }

                $statement->closeCursor();

            }

            if ($this->db_count == 0) {
                $this->db_error_message = 'No such record found';
                $this->db_error_code = null;
            }
            //mysql response/////////////

            return true;
        } catch (Exception $e) {
            $this->db_error_message = 'Build Query failed ' . $e->getMessage();
            $this->db_error_code = $e->getCode();
            return false;
        } finally {

            $this->reset_parameters();

        }
    }

    private final function build_insert_query($tableData)
    {
        if (!is_array($tableData)) {
            return;
        }

        $type = ''; //Update Insert MultiInsert

        if (preg_match('/^INSERT/', $this->db_query)) {
            if (isset($tableData[0]) && is_array($tableData[0]))
                $type = 'MultiInsert';
            else
                $type = 'Insert';
        } elseif (preg_match('/^CALL/', $this->db_query)) {
            $type = 'StoreProcedure';
        } else
            $type = 'Update';

        if ($type === 'StoreProcedure') {
            $this->db_query .= ' (';
            $dataColumns = array_keys($tableData);
            $this->_buildDataPairs($tableData, $dataColumns, $type);
            $this->db_query .= ') ';
        } else {
            if ($type === 'MultiInsert')
                $dataColumns = array_keys($tableData[0]);
            else
                $dataColumns = array_keys($tableData);

            if ($type === 'Update')
                $this->db_query .= " SET ";
            else {
                if (isset ($dataColumns[0]))
                    $this->db_query .= ' (`' . implode($dataColumns, '`, `') . '`) ';
                $this->db_query .= ' VALUES (';
            }

            if ($type === 'MultiInsert') {
                foreach ($tableData as $val) {
                    $this->_buildDataPairs($val, $dataColumns, $type);
                    $this->db_query .= '), (';
                }
                $this->db_query = rtrim($this->db_query, ', (');
            } else {
                $this->_buildDataPairs($tableData, $dataColumns, $type);
                if ($type !== 'Update') {
                    $this->db_query .= ')';
                }
            }
        }
    }

    private final function determine_type($item)
    {
        if ($this->db_connection_type === 'mySQLi') {
            switch (gettype($item)) {
                case 'NULL':
                case 'string':
                    return 's';
                    break;

                case 'boolean':
                case 'integer':
                    return 'i';
                    break;

                case 'blob':
                    return 'b';
                    break;

                case 'double':
                    return 'd';
                    break;
            }
        } else if ($this->db_connection_type === 'PDO') {
            switch (gettype($item)) {
                case 'NULL':
                case 'string':
                    return PDO::PARAM_STR;
                    break;
                case 'boolean':
                    return PDO::PARAM_BOOL;
                    break;
                case 'integer':
                    return PDO::PARAM_INT;
                    break;
            }
        }

        return '';
    }

    private final function bind_param($value)
    {
        if ($this->db_connection_type === 'mySQLi') {
            if (isset($this->db_bind_params[0]))
                $this->db_bind_params[0] .= $this->determine_type($value);
            else
                $this->db_bind_params[0] = $this->determine_type($value);
            array_push($this->db_bind_params, $value);
        } else if ($this->db_connection_type === 'PDO') {
            array_push($this->db_bind_params, array($value, $this->determine_type($value)));
        } else {

        }
    }

    private final function _buildDataPairs($tableData, $tableColumns, $type)
    {
        foreach ($tableColumns as $column) {
            $value = $tableData[$column];

            if ($type === 'Update') {
                if (strpos($column, '.') === false) {
                    $this->db_query .= "`" . $column . "` = ";
                } else {
                    $this->db_query .= str_replace('.', '.`', $column) . "` = ";
                }
            }

            if ($type === 'StoreProcedure' && $value == 'OUT') {
                $this->db_query .= '@' . $column . ', ';
                $this->db_output_procedure .= '@' . $column . ', ';
                continue;
            }
            if (!is_array($value)) // else
            {
                $this->bind_param($value);
                $this->db_query .= '?, ';
                continue;
            }

            // Function value
            $key = key($value);
            $val = $value[$key];
            switch ($key) {
                case '[I]':
                    $this->db_query .= $column . $val . ", ";
                    break;
                case '[F]':
                    $this->db_query .= $val[0] . ", ";
                    if (!empty($val[1])) {
                        $this->bind_param($value);
                    }
                    break;
                case '[N]':
                    if ($val == null) {
                        $this->db_query .= "!" . $column . ", ";
                    } else {
                        $this->db_query .= "!" . $val . ", ";
                    }
                    break;
                default:
                    throw new Exception("Wrong operation");
            }
        }
        $this->db_query = rtrim($this->db_query, ', ');
        if (!empty($this->db_output_procedure))
            $this->db_output_procedure = rtrim($this->db_output_procedure, ', ');
    }

    private final function _buildPair($operator, $value)
    {

        if (!is_object($value)) {
            $this->bind_param($value);
            return ' ' . $operator . ' ? ';
        }
        return;
    }

    private final function _buildGroupBy()
    {
        if (empty($this->db_group_by)) {
            return;
        }

        $this->db_query .= " GROUP BY ";

        foreach ($this->db_group_by as $key => $value) {
            $this->db_query .= $value . ", ";
        }

        $this->db_query = rtrim($this->db_query, ', ') . " ";
    }

    private final function _buildOrderBy()
    {
        if (empty($this->db_order_by)) {
            return;
        }

        $this->db_query .= " ORDER BY ";
        foreach ($this->db_order_by as $prop => $value) {
            if (strtolower(str_replace(" ", "", $prop)) == 'rand()') {
                $this->db_query .= "rand(), ";
            } else {
                $this->db_query .= $prop . " " . $value . ", ";
            }
        }

        $this->db_query = rtrim($this->db_query, ', ') . " ";
    }

    private final function _buildLimit($numRows)
    {
        if (!isset($numRows)) {
            return;
        }

        if (is_array($numRows)) {
            $this->db_query .= ' LIMIT ' . (int)$numRows[0] . ', ' . (int)$numRows[1];
        } else {
            $this->db_query .= ' LIMIT ' . (int)$numRows;
        }
    }

    private final function _buildCondition($operator, &$conditions)
    {
        if (empty($conditions)) {
            return;
        }
        $this->db_query .= ' ' . $operator;
        foreach ($conditions as $cond) {
            list ($concat, $varName, $operator, $val) = $cond;
            $this->db_query .= " " . $concat . " " . $varName;
            switch (strtolower($operator)) {
                case 'not in':
                case 'in':
                    $comparison = ' ' . $operator . ' (';
                    if (is_object($val)) {
                        $comparison .= $this->_buildPair("", $val);
                    } else {
                        foreach ($val as $v) {
                            $comparison .= ' ?,';
                            $this->bind_param($v);
                        }
                    }
                    $this->db_query .= rtrim($comparison, ',') . ' ) ';
                    break;
                case 'not between':
                case 'between':
                    $this->db_query .= " $operator ? AND ? ";
                    $this->bind_params($val);
                    break;
                case 'not exists':
                case 'exists':
                    $this->db_query .= $operator . $this->_buildPair("", $val);
                    break;
                case (isset($cond)):
                    foreach ($cond as $ke)
                        if (array_filter($cond, 'is_array')) {
                            if (is_array($ke)) {
                                $key = key($ke);
                                foreach ($ke as $keval) {
                                    $nowvalue = $keval;
                                    switch ($key) {
                                        case '[F]':
                                            $this->db_query .= $operator . ' ' . $nowvalue[0];
                                            break;
                                    }

                                }
                            }
                        } else {
                            if (is_array($val))
                                $this->bind_params($val);
                            if (is_array($val)) {
                                $this->db_query .= $this->_buildPair($operator, $val);

                            } elseif ($val === null) {
                                $this->db_query .= ' ' . $operator . " NULL";
                            } elseif ($val != 'DBNULL' || $val == '0') {
                                $this->db_query .= $this->_buildPair($operator, $val);
                            }
                            break;
                        }

                    break;

                default:
//                    if (is_array($val)) {
//                        $this->_bindParams($val);
                    if (key($val) != ['F'] && is_array($val)) {
                        $this->db_query .= $this->_buildPair($operator, $val);
                    } elseif ($val === null) {
                        $this->db_query .= ' ' . $operator . " NULL";
                    } elseif ($val != 'DBNULL' || $val == '0') {
                        $this->db_query .= $this->_buildPair($operator, $val);
                    }
            }
        }
    }

    private final function bind_params($values)
    {
        foreach ($values as $value) {
            $this->bind_param($value);
        }
    }

    private final function _buildJoin()
    {
        if (empty ($this->db_join))
            return;

        foreach ($this->db_join as $data) {
            list ($joinType, $joinTable, $joinCondition) = $data;


            if (is_object($joinTable))
                $joinStr = $this->_buildPair("", $joinTable);
            else
                $joinStr = $joinTable;

            $this->db_query .= " " . $joinType . " JOIN " . $joinStr . " on " . $joinCondition;
            // Add join and query
            if (!empty($this->db_joinAnd) && isset($this->db_joinAnd[$joinStr])) {
                foreach ($this->db_joinAnd[$joinStr] as $join_and_cond) {
                    list ($concat, $varName, $operator, $val) = $join_and_cond;
                    $this->db_query .= " " . $concat . " " . $varName;
                    $this->conditionToSql($operator, $val);
                }
            }


        }
    }

    private final function conditionToSql($operator, $val)
    {
        switch (strtolower($operator)) {
            case 'not in':
            case 'in':
                $comparison = ' ' . $operator . ' (';
                if (is_object($val)) {
                    $comparison .= $this->_buildPair("", $val);
                } else {
                    foreach ($val as $v) {
                        $comparison .= ' ?,';
                        $this->bind_params($v);
                    }
                }
                $this->db_query .= rtrim($comparison, ',') . ' ) ';
                break;
            case 'not between':
            case 'between':
                $this->db_query .= " $operator ? AND ? ";
                $this->bind_params($val);
                break;
            case 'not exists':
            case 'exists':
                $this->db_query .= $operator . $this->_buildPair("", $val);
                break;
            default:
                if (is_array($val))
                    $this->bind_params($val);
                else if ($val === null)
                    $this->db_query .= $operator . " NULL";
                else if ($val != 'DBNULL' || $val == '0')
                    $this->db_query .= $this->_buildPair($operator, $val);
        }
    }

    private final function reset_parameters_initial()
    {
        $this->db_result = array();
        $this->db_error_code = null;
        $this->db_error_message = null;
        $this->db_last_query = null;
        $this->db_last_insert_id = null;
        $this->db_count = null;
    }

    private final function reset_parameters()
    {
        $this->db_where = array();
        $this->db_join = array();
        $this->db_joinAnd = array();
        $this->db_order_by = array();
        $this->db_group_by = array();
        $this->db_bind_params = array();
        $this->db_query = null;
        $this->db_column = array();
        $this->db_table_name = null;
        $this->db_output_procedure = null;
        $this->db_export_file_path = null;
        $this->db_export_file_settings = null;
    }

    private final function generate_last_query($str, $vals)
    {
        if ($this->db_connection_type === 'PDO')
            $i = 0;
        else
            $i = 1;
        $newStr = "";

        if (empty($vals)) {
            return $str;
        }

        while ($pos = strpos($str, "?")) {
            if ($this->db_connection_type === 'PDO')
                $val = $vals[$i++][0];
            else
                $val = $vals[$i++];

            if (is_object($val)) {
                $val = '[object]';
            }
            if ($val === null) {
                $val = 'NULL';
            }
            $newStr .= substr($str, 0, $pos) . "'" . $val . "'";
            $str = substr($str, $pos + 1);
        }
        $newStr .= $str;
        return $newStr;
    }
}