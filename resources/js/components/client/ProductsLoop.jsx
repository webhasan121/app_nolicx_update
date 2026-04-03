import ProductCard from "../home/ProductCard";

export default function ProductsLoop({ products = [] }) {
    if (!products.length) return null;

    return (
        <div>
            <div
                className=""
                style={{
                    display: "grid",
                    justifyContent: "center",
                    gridTemplateColumns: "repeat(auto-fill, minmax(160px, 1fr))",
                    gridGap: "10px",
                }}
            >
                {products.map((product) => (
                    <div key={product.id} className="">
                        <ProductCard product={product} />
                    </div>
                ))}
            </div>
            <hr className="my-2" />
        </div>
    );
}
