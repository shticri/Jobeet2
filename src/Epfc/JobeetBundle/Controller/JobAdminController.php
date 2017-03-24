<?php

namespace Epfc\JobeetBundle\Controller;
 
use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery as ProxyQueryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
 
class JobAdminController extends Controller
{
    /**
     * @param ProxyQueryInterface $selectedModelQuery
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function batchActionExtend(ProxyQueryInterface $selectedModelQuery, Request $request)
    {
        if ($this->admin->isGranted('EDIT') === false || $this->admin->isGranted('DELETE') === false)
        {
            throw new AccessDeniedException();
        }
 
        $modelManager = $this->admin->getModelManager();
 
        $selectedModels = $selectedModelQuery->execute();
 
        try {
            foreach ($selectedModels as $selectedModel) {
                $selectedModel->extend();
                $modelManager->update($selectedModel);
            }
        } catch (\Exception $e) {
            //$this->addFlash('sonata_flash_error', 'flash_batch_merge_error');
            $this->get('session')->getFlashBag()->add('sonata_flash_error', $e->getMessage());
            
            return new RedirectResponse($this->admin->generateUrl('list', [
                'filter' => $this->admin->getFilterParameters()
            ]));
        }
 
        //$this->addFlash('sonata_flash_success', 'flash_batch_merge_success');
        $this->get('session')->getFlashBag()->add('sonata_flash_success',  sprintf('The selected jobs validity has been extended until %s.', date('m/d/Y', time() + 86400 * 30)));
 
        return new RedirectResponse($this->admin->generateUrl('list', [
            'filter' => $this->admin->getFilterParameters()
        ]));
    }
    
    /**
     * Manages the confirmation
     *
     */
    public function batchActionDeleteNeverActivatedIsRelevant()
    {
        return true;
    }

    /**
     * Removes all offers that have not been activated since more than 60 days
     *
     */
    public function batchActionDeleteNeverActivated()
    {
        if ($this->admin->isGranted('EDIT') === false || $this->admin->isGranted('DELETE') === false) {
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getEntityManager();
        $nb = $em->getRepository('JobeetBundle:Job')->cleanup(60);

        if ($nb) {
            $this->get('session')->getFlashBag()->add('sonata_flash_success',  sprintf('%d never activated jobs have been deleted successfully.', $nb));
        } else {
            $this->get('session')->getFlashBag()->add('sonata_flash_info',  'No job to delete.');
        }

        return new RedirectResponse($this->admin->generateUrl('list', [
            'filter' => $this->admin->getFilterParameters()
        ]));
    }
}

