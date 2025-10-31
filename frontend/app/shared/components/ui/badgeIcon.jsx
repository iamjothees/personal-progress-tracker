import { cn } from "@/lib/utils"
import { Badge } from "@/components/ui/badge"

export function BadgeIcon({
  icon: Icon,
  count = 0,
  className = "",
  badgeVariant = "destructive",
  showNumber = false,
  iconSize = "default",
  badgePosition = "top-right",
  ...props
}) {
  const sizeClasses = {
    sm: "h-4 w-4",
    default: "h-5 w-5",
    lg: "h-6 w-6"
  }

  const positionClasses = {
    "top-right": "-top-1 -right-1",
    "top-left": "-top-1 -left-1",
    "bottom-right": "-bottom-1 -right-1",
    "bottom-left": "-bottom-1 -left-1"
  }

  return (
    <div className={cn("relative inline-flex", className)} {...props}>
      <Icon className={cn(sizeClasses[iconSize], "text-current")} />
      {count > 0 && (
        <Badge
          variant={badgeVariant}
          className={cn(
            "absolute rounded-full p-0 h-4 w-4 flex items-center justify-center",
            positionClasses[badgePosition],
            !showNumber && "text-transparent"
          )}
        >
          {showNumber ? (count > 9 ? "9+" : count) : ""}
        </Badge>
      )}
    </div>
  )
}