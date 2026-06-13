export default function SupportButton({ whatsapp }) {
    if (!whatsapp) return null;

    return (
        <div>
            <a
                href={`https://wa.me/${whatsapp}`}
                title="Chat on Whatsapp"
                target="_blank"
                rel="noopener noreferrer"
                className="fixed flex items-center justify-center w-12 h-12 text-2xl text-white rounded-full shadow-xl"
                style={{
                    zIndex: 99999,
                    bottom: "38px",
                    right: "20px",
                    backgroundColor: "#25D366",
                }}
            >
                <i className="fab fa-whatsapp"></i>
            </a>
        </div>
    );
}
