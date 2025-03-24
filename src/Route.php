<?php
/**
 * Route handler
 *
 * @version    5.0
 * @package    Web
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */

 namespace App;

 use exception;

class Route
{
    public static $actions;
    public static $exception;
    
    /**
     * Register an action
     * 
     * @param $action entry point
     * @param $callback response callback
     */
    public static function add($action, $callback)
    {
        //var_dump($action);
        //var_dump($callback);
        self::$actions[$action] = $callback;
    }
    
    /**
     * Execute an action
     * 
     * @param $action entry point
     * @param $args arguments
     */
    public static function exec($action, $args)
    {
        call_user_func(self::$actions[$action], $args);
    }
    
    /**
     * Register an exception
     * 
     * @param $callback response callback
     */
    public static function exception($callback)
    {
        self::$exception = $callback;
    }
    
    /**
     * Execute the current URL action
     */
    public static function run()
{
    $args = $_REQUEST;
    
    if(isset($args['localizacao'])){
        
        $args['localizacao'] = str_replace('+', ' ', $args['localizacao']);
    }
    
    //var_dump($args['localizacao']);

    //var_dump($args);
    $callback = null;

    // Verifica se o parâmetro 'action' existe e é uma string
    if (isset($args['action']) && is_string($args['action'])) {

        // Se a ação contém barras, trata como URL amigável
        if (strpos($args['action'], '/') !== false) {
            $actionParts = explode('/', $args['action']);
            $actionName = array_shift($actionParts);
            $args['action'] = $actionName;
            $args['params'] = $actionParts;
        }

        // Procura o callback associado à ação
        if (isset(self::$actions[$args['action']])) {
            $callback = self::$actions[$args['action']];
        } else {
            // Ação não encontrada; redireciona para 404 ou trata como necessário
            header("Location: ./404");
            exit;
        }
    } elseif (isset(self::$actions[''])) {
        // Se nenhuma ação for definida, utiliza a ação padrão (caso exista)
        $callback = self::$actions[''];
    } else {
        // Caso nenhum cenário seja atendido, redireciona para 404
        header("Location: ./404");
        exit;
    }

    // Executa o callback, se for uma função válida
    if (is_callable($callback)) {
        try {
            call_user_func($callback, $args);
        } catch (Exception $e) {
            if (is_callable(self::$exception)) {
                call_user_func(self::$exception, $e);
            }
        }
    }
}
}
