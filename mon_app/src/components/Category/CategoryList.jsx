import React from "react";
import CategoryCard from "./CategoryCard";

const categories = [
  {
    id: 1,
    libelle: "Technologie",
    description: "Toutes les dernières innovations et tendances tech.",
    image: "https://source.unsplash.com/400x300/?technology",
  },
  {
    id: 2,
    libelle: "Voyage",
    description: "Explorez le monde à travers nos guides de voyage.",
    image: "https://source.unsplash.com/400x300/?travel",
  },
  {
    id: 3,
    libelle: "Cuisine",
    description: "Recettes, astuces et découvertes culinaires.",
    image: "https://source.unsplash.com/400x300/?cooking",
  },
];

const CategoryList = () => {
  return (
    <div className="p-6 max-w-7xl mx-auto">
      <h1 className="text-3xl font-bold mb-6">Catégories</h1>
      <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
        {categories.map((cat) => (
          <CategoryCard key={cat.id} category={cat} />
        ))}
      </div>
    </div>
  );
};

export default CategoryList;
