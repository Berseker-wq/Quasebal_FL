<?php
 
namespace App\Controller\Admin;
 
use App\Entity\Plat;
use App\Entity\Categorie;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
 
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
 
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
 
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
 
class PlatCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Plat::class;
    }
 
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('libelle', 'Nom du plat'),
            TextField::new('description'),
            MoneyField::new('prix')->setCurrency('EUR')->setStoredAsCents(false),
            ImageField::new('image')
                ->setBasePath('uploads/images')
                ->setUploadDir('public/uploads/images')
                ->setUploadedFileNamePattern('[slug]-[timestamp].[extension]')
                ->setRequired(false),
            AssociationField::new('categorie', 'Catégorie'),
            BooleanField::new('active', 'Actif'),
        ];
    }
 
    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('categorie'))
            ->add(BooleanFilter::new('active'));
    }
 
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->handleImageUpload($entityInstance);
        parent::persistEntity($entityManager, $entityInstance);
    }
 
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->handleImageUpload($entityInstance);
        parent::updateEntity($entityManager, $entityInstance);
    }
 
    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $imageName = $entityInstance->getImage();
 
        if ($imageName) {
            $imagePath = $this->getParameter('kernel.project_dir') . '/public/uploads/images/' . $imageName;
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
 
        parent::deleteEntity($entityManager, $entityInstance);
    }
 
    private function handleImageUpload(Plat $plat): void
    {
        $image = $plat->getImage();
 
        if ($image instanceof UploadedFile) {
            $safeFilename = preg_replace('/[^a-z0-9]+/', '-', strtolower($plat->getLibelle()));
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $image->guessExtension();
 
            // Déplacer le fichier dans le dossier uploads
            $image->move(
                $this->getParameter('kernel.project_dir') . '/public/uploads/images',
                $newFilename
            );
 
            // Mettre à jour le nom de l’image dans l’entité
            $plat->setImage($newFilename);
        }
    }
}
 