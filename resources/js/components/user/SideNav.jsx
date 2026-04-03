import { usePage, Link } from "@inertiajs/react";
import { useState } from "react";

export default function SideNav() {
    const { categories } = usePage().props;
    const [open, setOpen] = useState(false);

    if (!open) return null;

    return (
        <aside className="fixed top-0 left-0 w-64 h-screen bg-white shadow">
            {categories.map(cat => (
                <div key={cat.id} className="p-3 border-b">
                    <Link href={`/category/${cat.slug}/products`}>
                        {cat.name}
                    </Link>
                </div>
            ))}
        </aside>
    );
}
