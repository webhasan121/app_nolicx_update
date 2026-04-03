export default function Container({ children, className = "" }) {
    return (
        <div className={`my-3 ${className}`}>
            <div className="w-full px-2 mx-auto space-y-6 max-w-8xl sm:px-6 lg:px-8">
                {children}
            </div>
        </div>
    );
}
