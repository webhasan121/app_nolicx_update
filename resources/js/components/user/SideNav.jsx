import { usePage, Link } from "@inertiajs/react";
import { useState } from "react";
import useTranslation from "../../hooks/useTranslation";

export default function SideNav() {
    const { categories } = usePage().props;
    const [open, setOpen] = useState(false);
    const { t } = useTranslation();

    if (!open) return null;

    return (
        <aside className="fixed top-0 left-0 w-64 h-screen bg-white shadow">
            {categories.map(cat => (
                <div key={cat.id} className="p-3 border-b">
                    <Link href={`/category/${cat.slug}/products`}>
                        {t(cat.name)}
                    </Link>
                </div>
            ))}
        </aside>
    );
}
