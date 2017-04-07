<?php
namespace App\Controller;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use App\Model\NewsMapper;
use App\Model\NewsEntity;
use Symfony\Component\Validator\Constraints as Assert;
class Front
{
    public function getIndex(Request $request, Application $app)
    {
        $mapper = new NewsMapper($app['db']);
        $items = $mapper->getNews();
        $logged = $request->getSession()->get('logged');
        $app['view.name'] = 'login';
        return $app['view']->data(['items' => $items, 'logged' => $logged])->render();
        // return include '../templates/index.tpl.php';
    }

    public function getLogin(Request $request, Application $app)
    {
        $app['view.name'] = 'login';
        return $app['view']->render();
    }
    public function postLogin(Request $request, Application $app)
    {
        $login = $request->request->get('name');
        $pass = $request->request->get('pass');
        $session = $request->getSession();


        $errors = $app['validator']->validate(new Assert\Collection(array(
            $login => new Assert\Email(),
            $pass => array(new Assert\NotBlank(), new Assert\Length(array('min' => 3))),
        )));
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                echo $error->getPropertyPath().' '.$error->getMessage()."\n";
            }
        }

        if ($login == 'x@y' && $pass == '123') {
            $session->set('logged', true);
            return $app->redirect('/cabinet');
        }
        return $app->redirect('/login');
    }
    public function getLogout(Request $request, Application $app)
    {
        $session = $request->getSession();
        $session->clear();
        $session->invalidate();
        return $app->redirect('/');
    }
}