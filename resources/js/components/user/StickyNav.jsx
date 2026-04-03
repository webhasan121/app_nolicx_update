import { useEffect, useState } from "react";

export default function StickyNav() {
    const [visible, setVisible] = useState(false);

    useEffect(() => {
        const handleScroll = () => {
            setVisible(window.scrollY > 150);
        };

        window.addEventListener("scroll", handleScroll);
        return () => window.removeEventListener("scroll", handleScroll);
    }, []);

    if (!visible) return null;

    return (
        <div className="fixed top-0 z-50 w-full bg-white shadow">
            Sticky Nav
        </div>
    );
}
