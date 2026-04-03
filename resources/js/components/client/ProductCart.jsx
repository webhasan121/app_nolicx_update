import ProductCard from "../home/ProductCard";

export default function ProductCart({ product }) {
    if (!product) return null;
    return <ProductCard product={product} />;
}

