export default function Rating({ value }: { value: number }) {
    return (
        <span
            aria-label={`Rated ${value} out of 10`}
            className="inline-flex items-baseline gap-1 rounded-full bg-navy px-3 py-1 font-black text-white shadow-sm ring-1 ring-white/30"
        >
            <span>{value.toFixed(1)}</span>
            <span className="text-xs">/10</span>
        </span>
    );
}
