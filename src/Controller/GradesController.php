<?php

namespace App\Controller;

use App\Entity\Grade;
use App\Form\GradeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\SubjectRepository;
use Symfony\Contracts\Translation\TranslatorInterface;

class GradesController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em, private SubjectRepository $subjectRepository){}

    #[Route('/', name: 'app_grade')]
    public function index(Request $requets, TranslatorInterface $translator)
    {
        $subjects = $this->subjectRepository->findAll();

        if (empty($subjects)) {
            $this->addFlash('danger', $translator->trans('flash_messages.grade.error.subject'));
            return $this->redirectToRoute('app_subject');
        }

        $grades = $this->em->getRepository(Grade::class)->findAll();
        $grade = new Grade();
        $form = $this->createForm(GradeType::class, $grade);

        $grade->setCreatedAt(new \DateTimeImmutable());

        $form = $this->createForm(GradeType::class, $grade);

        $form->handleRequest($requets);
        if($form->isSubmitted() && $form->isValid()){
            $this->em->persist($grade);
            $this->em->flush();
            $this->addFlash('success', $translator->trans('flash_messages.grade.success.create'));
            return $this->redirectToRoute('app_grade');
        }

        return $this->render("grade/index.html.twig", [
            "controller_name" => "GradeController",
            "grades" => $grades,
            'form' => $form->createView()
        ]);

    }
}
