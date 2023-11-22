<?php

namespace App\Controller;

use App\Entity\Subject;
use App\Form\SubjectsType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

class SubjectsController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em){}

    #[Route('/subject', name: 'app_subject')]
    public function index(Request $requets, TranslatorInterface $translator)
    {
        $subjects = $this->em->getRepository(Subject::class)->findAll();
        $subject = new Subject();
        $form = $this->createForm(SubjectsType::class, $subject);

        $form->handleRequest($requets);
        if($form->isSubmitted() && $form->isValid()){
            $this->em->persist($subject);
            $this->em->flush();
            $this->addFlash('success', $translator->trans('flash_messages.subject.success.create'));
            return $this->redirectToRoute('app_subject');
        }

        return $this->render("subject/index.html.twig", [
            "controller_name" => "SubjectController",
            "subjects" => $subjects,
            'form' => $form->createView()
        ]);
    }

    #[Route('subject/{id}', name: 'app_subject_edit')]
    public function details(Subject $subject = null, Request $requets,TranslatorInterface $translator)
    {
        if ($subject == null) {
            return $this->redirectToRoute('app_subject');
        } 
    
        $form = $this->createForm(SubjectsType::class, $subject);
        $form->handleRequest($requets);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($subject);
            $this->em->flush();
            $this->addFlash('success', $translator->trans('flash_messages.subject.success.edit'));
        }
        
        return $this->render("subject/details.html.twig", [
            "controller_name" => "SubjectController",
            "detail" => $subject,
            'form' => $form->createView(),
        ]);
    }

    #[Route('subject/{id}/delete', name: 'app_subject_delete')]
    public function delete(Subject $subject = null,TranslatorInterface $translator)
    {
        if ($subject == null) {
            $this->addFlash('danger', $translator->trans('flash_messages.subject.error.not_found'));
            return $this->redirectToRoute('app_subject');
        }
    
        $subjectId = $subject->getId();
    
        // Delete the subject
        $this->em->remove($subject);
        $this->em->flush();
    
        // Delete associated grades
        $this->em->createQuery('DELETE FROM App\Entity\Grade g WHERE g.subject = :subjectId')
            ->setParameter('subjectId', $subjectId)
            ->execute();
    
        $this->addFlash('success',  $translator->trans('flash_messages.subject.success.delete'));
    
        return $this->redirectToRoute('app_subject');
    }
}
