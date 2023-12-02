<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    #[Route('/products/detail/{id}', name: 'app_detail')]
    public function detail($id,Request $request,EntityManagerInterface $entitymanager, ProductRepository $pr): Response
    {
        $product = $pr->find($id);

        return $this->render('product/detail.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/products', name: 'app_products')]
    public function list(Request $request,EntityManagerInterface $entitymanager): Response
    {
        $products = $entitymanager->getRepository(Product::class)->findAll();

        return $this->render('product/index.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/product/new', name: 'app_new_product')]
    #[Route('/product/edit/{id}', name: 'app_edit_product')]
    public function new_edit(Product $product = NULL,Request $request,EntityManagerInterface $entitymanager): Response
    {
        if(!$product){
            $product = new Product();
            $product->setUser($this->getUser());
        }

        $form = $this->createForm(ProductType::class, $product);
        
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $entitymanager->persist($product);
            $entitymanager->flush();
            $this->addFlash('success', 'Produit ajouté avec succès');
            return $this->redirectToRoute('app_detail', ['id' => $product->getId() ]);
        }

        return $this->render('product/new.html.twig', [
            'form' => $form,
            'edit' => $product->getId(),
        ]);
    }

    #[Route('/products/delete/{id}', name: 'app_delete')]
    public function delete(Product $product, Request $request,EntityManagerInterface $entitymanager): Response
    {
        $entitymanager->remove($product);
        $entitymanager->flush();
        $this->addFlash('success', 'Produit supprimé avec succès');
        return $this->redirectToRoute('app_products');
    }
}
