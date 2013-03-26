<?php

/**
 * Description of User
 *
 * @author James
 */
class App_Model_Proveedor extends App_Db_Table_Abstract
{

    protected $_name = 'proveedor';

    const ESTADO_ACTIVO = '1';
    const ESTADO_ELIMINADO = '0';
    const TABLA_PROVEEDOR = 'proveedor';
    
    /**
     * @param array $datos
     * @param string $condicion para el caso de actualizacion
     * @return int Identificador de la columna
     */
    private function _guardar($datos, $condicion = NULL) 
    {
        $id = 0;
        if (!empty($datos['idproveedor'])) {
            $id = (int) $datos['idproveedor'];
        } 
        
        unset($datos['idproveedor']);
        $datos = array_intersect_key($datos, array_flip($this->_getCols()));

        if ($id > 0) {
            $condicion = '';
            if (!empty($condicion)) {
                $condicion = ' AND ' . $condicion;
            }

            $cantidad = $this->update($datos, 'idproveedor = ' . $id . $condicion);
            $id = ($cantidad < 1) ? 0 : $id;
        } else {
            $id = $this->insert($datos);
        }
        return $id;
    }

    public function getProveedorPorId($id) 
    {
        $query = $this->getAdapter()->select()
                ->from($this->_name)
                ->where('idproveedor = ?', $id)
                ->where('estado = ?', '1')
                ;

        return $this->getAdapter()->fetchRow($query);
    }

    public function listarProveedor() 
    {
        $query = $this->getAdapter()
                ->select()->from(array('p' => $this->_name))
                ->limit(50);

        return $this->getAdapter()->fetchAll($query);
    }

    public function actualizarDatos($datos) 
    {
        return $this->_guardar($datos);
    }
    
    public function buscarProveedor(array $data = array()) 
    {

        $db = $this->getAdapter();

        $select = $db->select()
                ->from(array('u' => $this->_name), $this->_getCols())
                ->where('u.estado = ?', self::ESTADO_ACTIVO)
                ->where('u.idTipoUsuario = ?', App_Model_User::TIPO_CLIENTE);

        if (isset ($data['idCliente']) and !empty($data['idCliente']))
            $select->where('u.idCliente = ?', $data["idCliente"]);

        if (isset ($data["email"]) and !empty($data["email"]))
            $select->where('u.correo like ?', "%{$data["email"]}%");

        if (isset($data["nombre"]) and !empty($data["nombre"])) {
            $concat = new Zend_Db_Expr("CONCAT(TRIM(u.nombreCliente), ' ', TRIM(u.apellidoCliente))");
            $select->where("$concat like ?", "%{$data["nombre"]}%");
        }        
        if (isset($data["apellido"]) and !empty($data["apellido"])) {
            $concat = new Zend_Db_Expr("CONCAT(TRIM(u.nombreCliente), ' ', TRIM(u.apellidoCliente))");
            $select->where("$concat like ?", "%{$data["apellido"]}%");
        }        
        
        $select->order('idCliente')
                ->limit(50);

        return $db->fetchAll($select);
    }
    
    public function proveedorCombo() 
    {
        $query = $this->getAdapter()
                ->select()->from(array('p' => $this->_name), 
                    array('idproveedor', 'razonSocial'))
                ->where('p.estado = ?', App_Model_Proveedor::ESTADO_ACTIVO)
                ->limit(50);

        return $this->getAdapter()->fetchPairs($query);
    }

}