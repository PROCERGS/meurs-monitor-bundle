<?php

namespace PROCERGS\LoginCidadao\MonitorBundle\Controller;

use Liip\MonitorBundle\Runner;
use Liip\MonitorBundle\Helper\ArrayReporter;
use Symfony\Component\HttpFoundation\Response;
use ZendDiagnostics\Runner\Reporter\ReporterInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class StatusController extends Controller
{
    /**
     * @Route("/public/status/{check}", defaults={"check" = null})
     * @Template()
     */
    public function indexAction($check)
    {
        $runner = $this->getRunner();

        $reporter = new ArrayReporter();
        $runner->addReporter($reporter);
        $runner->run($check);

        $results = $this->prepareResults($reporter);

        if (count($results['results']) === 1) {
            $result = reset($results['results']);
            return new Response($result['result'], $results['statusCode']);
        }

        $template = 'PROCERGSLoginCidadaoMonitorBundle:Status:index.html.twig';
        $response = new Response('', $results['statusCode']);

        return $this->render($template, $results, $response);
    }

    /**
     *
     * @return Runner|object
     */
    private function getRunner()
    {
        return $this->get('liip_monitor.runner');
    }

    private function prepareResults(ReporterInterface $reporter)
    {
        $results    = array();
        $statusCode = 200;
        foreach ($reporter->getResults() as $result) {
            $status = array(
                'check' => $result['checkName'],
                'code' => $result['status']
            );
            if ($result['status'] === 0) {
                $status['result'] = '0-OK';
            } else {
                $status['result'] = '1-NOK';

                $statusCode = 503;
            }

            $results[] = $status;
        }

        return array(
            'statusCode' => $statusCode,
            'results' => $results
        );
    }
}
