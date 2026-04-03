import { Link } from "@inertiajs/react";

export default function ProductSection({ title, products, viewAll }) {
    return (
        <div className="py-10">
            <div className="flex justify-between mb-6">
                <h2 className="text-xl font-bold">{title}</h2>
                <Link href={viewAll}>View All</Link>
            </div>

            <div className="grid grid-cols-2 gap-4 md:grid-cols-4">
                {products.map(product => (
                    <div key={product.id} className="p-3 border">
                        <img
                            src={`/storage/${product.thumbnail}`}
                            className="object-cover w-full h-40"
                        />
                        <h3>{product.name}</h3>
                        <p>{product.price}</p>
                    </div>
                ))}
            </div>
        </div>
    );
}
