<?php namespace Arsenetoumani\Breadcrumb;

use Str, Route, Config;

class Breadcrumb {

	private function getCurrentRoute()
	{
		$routeArray = Str::parseCallback(Route::currentRouteAction(), null);

        if (last($routeArray) != null) {
            // Get module
            $arr1 = explode('\Modules\\', head($routeArray));
            $arr2 = explode('\\', last($arr1));
            $module = head($arr2);

            // Remove 'controller' from the controller name.
            $controller = str_replace('Controller', '', class_basename(head($routeArray)));

            // Take out the method from the action.
            $action = str_replace(array('get', 'post', 'patch', 'put', 'delete'), '', last($routeArray));

            $currentRoute = array(
                'module' => $module,
            	'controller' => $controller,
                'controllerPath' => head($routeArray),
            	'action' => $action,
                'parameters' => Route::current()->parameters()
            );

            return $currentRoute;
        }

        return false;
	} 

    public function getBreadcrumb() {

        $currentRoute = $this->getCurrentRoute();
        $controller   = $currentRoute['controller'];
        $action       = $currentRoute['action'];
        $parameters   = $currentRoute['parameters'];

        // Create vars
        $home_block = ''; $controller_block = ''; $action_block = ''; $parameters_block = ''; $home_anchor = ''; $controller_anchor = ''; $action_anchor = '';

        // Anchors
        if (!empty($controller) && $controller != 'Home') $home_anchor = array('<a href="' . route('findMember') . '">', '</a>');
        if (!empty($action) && $action != 'Home') $controller_anchor = array('<a href="' . action($currentRoute['controllerPath'] . '@getIndex') . '">', '</a>');
        if (!empty($parameters)) $action_anchor = array('<a href="' . action($currentRoute['controllerPath'] . '@get' . $action) . '">', '</a>');

        // Home block
        if (is_array($home_anchor)) {
            $home_block = '<li>' . $home_anchor[0] . 'Home' . $home_anchor[1] . '</li>';
        }
        // Controller block
        if (!empty($controller)) {
            if (is_array($controller_anchor)) {
                $controller_block = '<li>' . $controller_anchor[0] . ucwords($controller) . $controller_anchor[1] . '</li>';
            } else {
                if ($controller == 'home') $controller_block = '<li> ' . ucwords($controller) . '</li>';
                else $controller_block = '<li>' . ucwords($controller) . '</li>';
            }
        }
        // Action block
        if (!empty($action)) {
            if (is_array($action_anchor)) {
                $action_block = '<li>' . $action_anchor[0] . ucwords($action) . $action_anchor[1] . '</li>';
            } else {
                $action_block = '<li>' . ucwords($action) . '</li>';
            }
        }
        // Parameter block
        if (!empty($parameters)) {
            if( strlen(head($parameters)) > 22 ) $param_parsed = substr(head($parameters), 0, 22) . '..';
            else $param_parsed = head($parameters);

            $param_parsed = str_replace('-', ' ', $param_parsed);
            $parameters_block = '<li>' . $param_parsed . '</li>';
        }

        // Conditions
        if ($action == 'View' || $action == 'Index') $action_block = '';
        if ($controller == 'FindMember') $controller_block = '';

        // Breadcrumb
        $breadcrumb = $home_block . $controller_block . $action_block . $parameters_block;

        return $breadcrumb;
    }
}