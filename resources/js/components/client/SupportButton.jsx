export default function SupportButton({ whatsapp }) {
    if (!whatsapp) return null;

    return (
        <div>
            <a
                href={`https://wa.me/${whatsapp}`}
                title="Chat on Whatsapp"
                className="flex justify-center items-center rounded-full text-4xl text-white shadow-xl w-16 h-16 fixed"
                style={{ zIndex: 99999, bottom: "35px", right: "20px", backgroundColor: "#25D366" }}
                target="_blank"
                rel="noreferrer"
            >
                <i className="fab fa-whatsapp fs-3x"></i>
            </a>
        </div>
    );
}
