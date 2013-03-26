<?php

class Admin_ProductoController extends App_Controller_Action_Admin
{

    public function init()
    {
        parent::init();
    }
    
    public function indexAction()
    {
        $model = new App_Model_Producto();
        $this->view->producto = $model->listaProductos();
    } 
    
    public function crearAction()
    {   
        
        $form = new App_Form_CrearProducto();
        $this->view->form = $form;         
        $idUsuario = $this->view->authData->idusuario;
        
        if($this->getRequest()->isPost()){            
            $data = $this->getRequest()->getPost();
            
            if ($form->isValid($data)) {
                $modelProducto = new App_Model_Producto();
                
                $subcate =  $form->subcategoria->getValue();
                $fecha = Zend_Date::now()->toString('YYYY-MM-dd HH:mm:ss');
                
                $datos = array(
                    'codigo' => $data['codigo'],
                    'nombreProducto' => $data['nombreProducto'],
                    'idservicio' => $data['idservicio'],
                    'idcategoria' => $subcate,
                    'idproveedor' => $data['proveedor'],
                    'descripcionCorta' => $data['descripcionCorta'],
                    'descripcionLarga' => $data['descripcionLarga'],
                    'fechaRegistro' => $fecha,
                    'fechaInicio' => $data['fechaInicio'],
                    'fechaFin' => $data['fechaFin'],
                    'precio' => $data['precio'],
                    'stock' => $data['stock'],
                    'idUsuarioRegistro' => $idUsuario,
                    'estado' => App_Model_Producto::ESTADO_ACTIVO,                    
                );
                
                
                $id = $modelProducto->actualizarDatos($datos);
                
                $modelFoto = new App_Model_ProductoFoto();
                
                
                $config = Zend_Registry::get('config');
                $ruta = $config->app->mediaRoot;
                
                $fotoPrincipal = $form->fotoPrincipal->getFileName();
                if (!empty ($fotoPrincipal)) {
                    $form->fotoPrincipal->addFilter(
                        'Rename', 
                        array('target' => $ruta . $id . "-Principal.jpeg", 'overwrite' => true)
                    );
                    $form->fotoPrincipal->receive();
                    $data['idProducto'] = $id;
                    $data['foto'] = $id . ".jpeg";
                    $dataFoto = array(
                        'idproducto' => $id,
                        'foto' => $id . "-Principal.jpeg",
                        'orden' => '0'
                    );
                    $modelFoto->actualizarDatos($dataFoto);
                }
                
                /* foto 1*/
                $foto1 = $form->foto1->getFileName();
                if (!empty ($foto1)) {
                    $form->foto1->addFilter(
                        'Rename', 
                        array('target' => $ruta . $id . "-1.jpeg", 'overwrite' => true)
                    );
                    $form->foto1->receive();
                    $dataFoto = array(
                        'idproducto' => $id,
                        'foto' => $id . "-1.jpeg",
                        'orden' => '1'
                    );                    
                    $modelFoto->actualizarDatos($dataFoto);
                }
                /* foto 2*/
                $foto2 = $form->foto2->getFileName();
                if (!empty ($foto2)) {
                    $form->foto2->addFilter(
                        'Rename', 
                        array('target' => $ruta . $id . "-2.jpeg", 'overwrite' => true)
                    );
                    $form->foto2->receive();
                    $dataFoto = array(
                        'idproducto' => $id,
                        'foto' => $id . "-2.jpeg",
                        'orden' => '2'
                    );
                    $modelFoto->actualizarDatos($dataFoto);
                }
                /* foto 3*/
                $foto3 = $form->foto3->getFileName();
                if (!empty ($foto3)) {
                    $form->foto3->addFilter(
                        'Rename', 
                        array('target' => $ruta . $id . "-3.jpeg", 'overwrite' => true)
                    );
                    $form->foto3->receive();
                    
                    $dataFoto = array(
                        'idproducto' => $id,
                        'foto' => $id . "-3.jpeg",
                        'orden' => '3'
                    );                    
                    $modelFoto->actualizarDatos($dataFoto);
                }
                /* foto 4*/
                $foto4 = $form->foto4->getFileName();
                if (!empty ($foto4)) {
                    $form->foto4->addFilter(
                        'Rename', 
                        array('target' => $ruta . $id . "-4.jpeg", 'overwrite' => true)
                    );
                    $form->foto4->receive();
                    $dataFoto = array(
                        'idproducto' => $id,
                        'foto' => $id . "-4.jpeg",
                        'orden' => '4'
                    );                    
                    $modelFoto->actualizarDatos($dataFoto);
                }
                
                
                
                $this->_flashMessenger->addMessage("Guardado con éxito");
                $this->_redirect('/admin/producto');
                
                
                
            } else {
                //var_dump($form->getMessages());exit;
                $form->populate($data);                
            }
        }        
        
    } 

    public function editarAction()
    {
        $modelProducto = new App_Model_Producto();
        $form = new App_Form_CrearProducto();
        
        $id = $this->_getParam('id');
        $producto = $modelProducto->getProductoPorId($id);
        $form->populate($producto);
         
        if($this->getRequest()->isPost()){
            $data = $this->getRequest()->getPost();            
            $data['idProducto'] = $id;            
            if ($form->isValidPartial(
                    array('nombreProducto' => $data['nombreProducto'], 
                        'precio' => $data['precio']))) {
                $modelProducto = new App_Model_Producto();
                $data['usuarioRegistro'] = $this->view->authData->idUsuario;                
                $cond = array_key_exists("fotoAnt", $data);
                
                if (!$cond) {
                    $foto = $form->foto->getFileName();                    
                    if (!empty ($foto)) {
                        $data['foto'] = $id . ".jpeg";

                        $config = Zend_Registry::get('config');
                        $ruta = $config->app->mediaRoot;

                        $form->foto->addFilter(
                                'Rename', array(
                            'target' => $ruta . $id . ".jpeg",
                            'overwrite' => true)
                        );
                        $form->foto->receive();
                    }
                }                
                $id = $modelProducto->actualizarDatos($data);
                
                $this->_flashMessenger->addMessage("Producto editado con éxito");
                $this->_redirect('/producto/');
            
            } else {                
                $form->populate($data);
                $this->_flashMessenger->addMessage("Verifique sus datos");                
            }
        }
        $this->view->ruta = $this->config->app->mediaRoot;
        $this->view->form = $form;
        $this->view->producto = $producto;
    }

}

