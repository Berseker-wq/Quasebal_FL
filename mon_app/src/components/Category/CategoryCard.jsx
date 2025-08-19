import React from "react";

const CategoryCard = ({ category }) => {
  return (
    <div className="bg-white shadow-md rounded-2xl p-4 hover:shadow-xl transition duration-300">
      <img
        src={category.image}
        alt={category.libelle}
        className="w-full h-40 object-cover rounded-xl mb-4"
      />
      <h3 className="text-xl font-semibold mb-2">{category.libelle}</h3>
      <p className="text-gray-600 text-sm">{category.description}</p>
    </div>
  );
};

export default CategoryCard;
